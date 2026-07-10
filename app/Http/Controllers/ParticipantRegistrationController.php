<?php

namespace App\Http\Controllers;

use App\Http\Requests\RegisterParticipantRequest;
use App\Mail\ParticipantRegistrationReceived;
use App\Mail\ParticipantRegistrationUpdated;
use App\Models\EventSetting;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use App\Models\RaceModality;
use App\Payments\CheckoutRequest;
use App\Payments\PaymentGateway;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Throwable;

class ParticipantRegistrationController extends Controller
{
    public function __construct(private readonly PaymentGateway $paymentGateway) {}

    public function store(RegisterParticipantRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $kit = Kit::query()->findOrFail($validated['kit_id']);
        unset(
            $validated['accepted_regulation'],
            $validated['accepted_privacy_policy'],
            $validated['accepted_fitness_declaration'],
            $validated['accepted_data_confirmation'],
        );

        try {
            [$registration, $raceModality] = DB::transaction(function () use ($request, $validated): array {
                $eventSetting = EventSetting::query()->lockForUpdate()->first();
                $raceModality = RaceModality::query()->lockForUpdate()->findOrFail($validated['race_modality_id']);

                if (! $raceModality->is_active) {
                    throw ValidationException::withMessages(['race_modality_id' => 'Escolha uma prova ativa.']);
                }

                if (! $raceModality->acceptsBirthDate(Carbon::parse($validated['birth_date']))) {
                    throw ValidationException::withMessages([
                        'birth_date' => "A idade do atleta na data da prova não atende à faixa etária: {$raceModality->ageRangeLabel()}.",
                    ]);
                }

                if ($eventSetting?->registrationDeadlineHasPassed()) {
                    throw ValidationException::withMessages(['registration' => 'O prazo para inscrições foi encerrado.']);
                }

                if ($eventSetting?->registrationLimitHasBeenReached()) {
                    throw ValidationException::withMessages(['registration' => 'O limite total de inscrições foi atingido.']);
                }

                if ($raceModality->participantLimitHasBeenReached()) {
                    throw ValidationException::withMessages(['race_modality_id' => 'O limite de inscrições desta prova foi atingido.']);
                }

                if (ParticipantRegistration::query()->where('participant_cpf', $validated['participant_cpf'])->exists()) {
                    throw ValidationException::withMessages(['participant_cpf' => 'Este atleta já possui uma inscrição.']);
                }

                $registration = ParticipantRegistration::create([
                    ...$validated,
                    'privacy_policy_accepted_at' => now(),
                    'privacy_policy_version' => ParticipantRegistration::PrivacyPolicyVersion,
                    'privacy_policy_acceptance_ip' => $request->ip(),
                    'privacy_policy_acceptance_user_agent' => $request->userAgent(),
                    'data_confirmation_accepted_at' => now(),
                    'data_confirmation_acceptance_ip' => $request->ip(),
                    'data_confirmation_acceptance_user_agent' => $request->userAgent(),
                    'modality' => $raceModality->displayName(),
                ]);

                return [$registration, $raceModality];
            }, attempts: 3);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000' && str_contains($exception->getMessage(), 'registration_identity')) {
                throw ValidationException::withMessages(['participant_cpf' => 'Este atleta já possui uma inscrição.']);
            }

            throw $exception;
        }

        if ($this->shouldCreateCheckout($kit)) {
            try {
                $checkout = $this->paymentGateway->createCheckout(new CheckoutRequest(
                    registration: $registration,
                    raceModality: $raceModality,
                    kit: $kit,
                    successUrl: $this->paymentSuccessUrl($registration),
                    cancelUrl: route('registration.payment.cancel'),
                    expiredUrl: route('registration.payment.expired'),
                ));

                $registration->update([
                    'payment_gateway' => $checkout->gateway,
                    'payment_gateway_reference' => $checkout->reference,
                    'payment_checkout_url' => $checkout->checkoutUrl,
                ]);

                Mail::to($registration->email)->send(new ParticipantRegistrationReceived($registration));

                return redirect()->away($checkout->checkoutUrl);
            } catch (Throwable $exception) {
                report($exception);

                return back()
                    ->withInput()
                    ->withErrors([
                        'checkout' => $this->checkoutErrorMessage($exception),
                    ]);
            }
        }

        Mail::to($registration->email)->send(new ParticipantRegistrationReceived($registration));

        return to_route('registration')
            ->with('status', "Inscrição enviada com sucesso. Protocolo: {$registration->protocol_number}. A confirmação de pagamento continua pendente.");
    }

    public function paymentSuccess(ParticipantRegistration $registration): RedirectResponse
    {
        if ($registration->payment_status !== 'paid') {
            $registration->update([
                'payment_status' => 'paid',
            ]);

            Mail::to($registration->email)->send(new ParticipantRegistrationUpdated($registration));
        }

        return to_route('registration')
            ->with('status', 'Pagamento recebido. Sua inscrição foi confirmada.');
    }

    public function paymentSuccessNotice(): RedirectResponse
    {
        return to_route('registration')
            ->with('status', 'Pagamento recebido pelo checkout. A inscrição será confirmada após a conciliação automática.');
    }

    public function paymentCancel(): RedirectResponse
    {
        return to_route('registration')
            ->with('status', 'Checkout cancelado. Sua inscrição ficou registrada com pagamento pendente.');
    }

    public function paymentExpired(): RedirectResponse
    {
        return to_route('registration')
            ->with('status', 'Checkout expirado. Sua inscrição ficou registrada com pagamento pendente.');
    }

    private function shouldCreateCheckout(Kit $kit): bool
    {
        if ((float) $kit->price <= 0) {
            return false;
        }

        return PaymentGatewaySetting::current()->isConfigured();
    }

    private function paymentSuccessUrl(ParticipantRegistration $registration): string
    {
        $settings = PaymentGatewaySetting::current();
        $minutesToExpire = (int) ($settings->checkout_minutes_to_expire ?: 60);

        return URL::temporarySignedRoute(
            'registration.payment.success',
            now()->addMinutes($minutesToExpire + 30),
            ['registration' => $registration]
        );
    }

    private function checkoutErrorMessage(Throwable $exception): string
    {
        if ($exception instanceof RequestException) {
            $errors = collect($exception->response->json('errors', []))
                ->pluck('description')
                ->filter()
                ->implode(' ');

            if (filled($errors)) {
                return "Não foi possível abrir o checkout: {$errors}";
            }
        }

        return 'Não foi possível abrir o checkout. Revise os dados do pagador e tente novamente.';
    }
}
