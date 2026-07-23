<?php

namespace App\Http\Controllers;

use App\Actions\CreateShirtOrder;
use App\Http\Requests\CheckPathfinderEligibilityRequest;
use App\Http\Requests\RegisterParticipantRequest;
use App\Http\Requests\StorePixReceiptRequest;
use App\Mail\ParticipantRegistrationReceived;
use App\Models\EventSetting;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use App\Models\PaymentGatewaySetting;
use App\Models\RaceModality;
use App\Models\Shirt;
use App\Payments\CheckoutRequest;
use App\Payments\PaymentGateway;
use App\Support\PixPayload;
use Illuminate\Database\QueryException;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Throwable;

class ParticipantRegistrationController extends Controller
{
    public function __construct(
        private readonly PaymentGateway $paymentGateway,
        private readonly CreateShirtOrder $createShirtOrder,
    ) {}

    public function store(RegisterParticipantRequest $request): RedirectResponse
    {
        $validated = $request->validated();
        $shirtId = $validated['shirt_id'] ?? null;
        $extraShirtSize = $validated['extra_shirt_size'] ?? null;
        $extraShirtQuantity = $validated['extra_shirt_quantity'] ?? null;
        unset($validated['shirt_id'], $validated['extra_shirt_size'], $validated['extra_shirt_quantity']);
        unset(
            $validated['accepted_regulation'],
            $validated['accepted_privacy_policy'],
            $validated['accepted_fitness_declaration'],
            $validated['accepted_data_confirmation'],
            $validated['accepted_special_kit_rules'],
        );
        try {
            [$registration, $raceModality, $kit] = DB::transaction(function () use ($request, $validated, $shirtId, $extraShirtSize, $extraShirtQuantity): array {
                $eventSetting = EventSetting::query()->lockForUpdate()->first();
                $raceModality = RaceModality::query()->lockForUpdate()->findOrFail($validated['race_modality_id']);
                $kit = Kit::query()->lockForUpdate()->findOrFail($validated['kit_id']);

                if (! $raceModality->is_active) {
                    throw ValidationException::withMessages(['race_modality_id' => 'Escolha uma prova ativa.']);
                }

                if (! $kit->is_active) {
                    throw ValidationException::withMessages(['kit_id' => 'Escolha um pacote ativo.']);
                }

                if ($kit->quantityLimitHasBeenReached()) {
                    throw ValidationException::withMessages(['kit_id' => 'A quantidade disponível deste pacote foi esgotada.']);
                }

                if (! $raceModality->acceptsBirthDate(Carbon::parse($validated['birth_date']), $eventSetting?->eventDateForAgeCalculation())) {
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

                $pathfinder = null;

                if ($kit->type === Kit::TypePathfinder) {
                    $pathfinder = Pathfinder::query()
                        ->where('cpf', $validated['participant_cpf'])
                        ->where('is_active', true)
                        ->lockForUpdate()
                        ->first();

                    if ($pathfinder === null || $pathfinder->registration()->exists()) {
                        throw ValidationException::withMessages([
                            'participant_cpf' => $pathfinder === null
                                ? 'Este CPF não está habilitado para o pacote de desbravadores.'
                                : 'Este desbravador já possui uma inscrição.',
                        ]);
                    }
                }

                $registration = ParticipantRegistration::create([
                    ...$validated,
                    'pathfinder_id' => $pathfinder?->id,
                    'shirt_size' => $kit->has_shirt ? ($validated['shirt_size'] ?? null) : null,
                    'regulation_accepted_at' => now(),
                    'regulation_version' => hash('sha256', (string) $eventSetting?->regulation),
                    'regulation_acceptance_ip' => $request->ip(),
                    'regulation_acceptance_user_agent' => $request->userAgent(),
                    'privacy_policy_accepted_at' => now(),
                    'privacy_policy_version' => ParticipantRegistration::PrivacyPolicyVersion,
                    'privacy_policy_acceptance_ip' => $request->ip(),
                    'privacy_policy_acceptance_user_agent' => $request->userAgent(),
                    'data_confirmation_accepted_at' => now(),
                    'data_confirmation_acceptance_ip' => $request->ip(),
                    'data_confirmation_acceptance_user_agent' => $request->userAgent(),
                    'special_kit_rules_accepted_at' => $kit->requiresRulesAcknowledgement() ? now() : null,
                    'special_kit_rules_version' => $kit->requiresRulesAcknowledgement()
                        ? (filled($kit->rules) ? hash('sha256', (string) $kit->rules) : ParticipantRegistration::SpecialKitRulesVersion)
                        : null,
                    'special_kit_rules_acceptance_ip' => $kit->requiresRulesAcknowledgement() ? $request->ip() : null,
                    'special_kit_rules_acceptance_user_agent' => $kit->requiresRulesAcknowledgement() ? $request->userAgent() : null,
                    'modality' => $raceModality->displayName(),
                    'result_category' => ParticipantRegistration::resultCategoryFor(
                        $validated['sex'],
                        Carbon::parse($validated['birth_date']),
                        $raceModality->ageReferenceDate($eventSetting?->eventDateForAgeCalculation()),
                    ),
                ]);

                if ($shirtId !== null) {
                    $this->createShirtOrder->handle(Shirt::query()->findOrFail($shirtId), [
                        'customer_name' => $registration->athlete_name,
                        'customer_email' => $registration->email,
                        'customer_phone' => $registration->phone,
                        'size' => $extraShirtSize,
                        'quantity' => (int) $extraShirtQuantity,
                    ], $registration);
                }

                return [$registration, $raceModality, $kit];
            }, attempts: 3);
        } catch (QueryException $exception) {
            if ($exception->getCode() === '23000' && str_contains($exception->getMessage(), 'registration_identity')) {
                throw ValidationException::withMessages(['participant_cpf' => 'Este atleta já possui uma inscrição.']);
            }

            throw $exception;
        }

        $registration->load('kit', 'shirtOrders.shirt');

        if ((float) $kit->price > 0 && PaymentGatewaySetting::current()->hasManualPix()) {
            Mail::to($registration->email)->send(new ParticipantRegistrationReceived($registration));

            return redirect()->to(URL::temporarySignedRoute(
                'registration.pix.show',
                now()->addDays(7),
                ['registration' => $registration],
            ));
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

    public function checkPathfinderEligibility(CheckPathfinderEligibilityRequest $request): JsonResponse
    {
        $isEligible = Pathfinder::query()
            ->where('cpf', $request->validated('cpf'))
            ->where('is_active', true)
            ->whereDoesntHave('registration')
            ->exists();

        return response()->json(['eligible' => $isEligible]);
    }

    public function showPix(ParticipantRegistration $registration, PixPayload $pixPayload): View
    {
        $settings = PaymentGatewaySetting::current();
        abort_unless($settings->hasManualPix(), 404);

        $registration->loadMissing('kit');
        abort_if($registration->kit === null, 404);

        $amount = $registration->priceFor($registration->kit);
        $eventSettings = EventSetting::current();
        $payload = $pixPayload->generate(
            key: $settings->pix_key,
            amount: $amount,
            receiverName: $settings->pix_account_holder ?: $settings->pix_receiver_name ?: $eventSettings->organizer_legal_name ?: config('app.name'),
            receiverCity: $settings->pix_receiver_city ?: $eventSettings->event_location ?: 'Brasil',
            transactionId: $registration->protocol_number,
        );

        return view('registration-pix', [
            'registration' => $registration,
            'pixKey' => $settings->pix_key,
            'amount' => $amount,
            'pixPayload' => $payload,
            'pixQrCode' => $pixPayload->qrCodeDataUri($payload),
            'pixBank' => $settings->pix_bank,
            'pixAgency' => $settings->pix_agency,
            'pixAccount' => $settings->pix_account,
            'pixAccountHolder' => $settings->pix_account_holder,
        ]);
    }

    public function storePixReceipt(StorePixReceiptRequest $request, ParticipantRegistration $registration): RedirectResponse
    {
        abort_unless(PaymentGatewaySetting::current()->hasManualPix(), 404);

        $path = $request->file('pix_receipt')->store('pix-receipts', 'local');

        $registration->update([
            'billing_name' => $request->string('billing_name')->toString(),
            'billing_document' => $request->string('billing_document')->toString(),
            'pix_receipt_path' => $path,
            'pix_receipt_submitted_at' => now(),
            'payment_status' => 'under_review',
            'bib_number' => $registration->bib_number ?? ParticipantRegistration::generateUniqueBibNumber(),
        ]);

        return redirect()->to(URL::temporarySignedRoute(
            'athlete.show',
            now()->addDays(7),
            ['registration' => $registration],
        ))->with('status', 'Comprovante enviado. Sua inscrição está em análise.');
    }

    public function paymentSuccess(ParticipantRegistration $registration): RedirectResponse
    {
        if ($registration->payment_status !== 'paid') {
            $registration->update([
                'payment_status' => 'paid',
            ]);
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

        $settings = PaymentGatewaySetting::current();

        return ! $settings->hasManualPix() && $settings->isConfigured();
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
