<?php

namespace App\Http\Controllers;

use App\Actions\CreateShirtOrder;
use App\Http\Requests\StorePixReceiptRequest;
use App\Http\Requests\StoreShirtOrderRequest;
use App\Mail\ShirtOrderReceived;
use App\Models\EventSetting;
use App\Models\PaymentGatewaySetting;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use App\Payments\CheckoutRequest;
use App\Payments\PaymentGateway;
use App\Support\PixPayload;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Throwable;

class ShirtOrderController extends Controller
{
    public function __construct(private PaymentGateway $paymentGateway) {}

    public function index(): View
    {
        return view('shirts', ['shirts' => Shirt::query()->where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(StoreShirtOrderRequest $request, CreateShirtOrder $action): RedirectResponse
    {
        $data = $request->validated();
        $shirt = Shirt::query()->findOrFail($data['shirt_id']);
        unset($data['shirt_id']);

        $shirtOrder = DB::transaction(fn () => $action->handle($shirt, $data));
        $shirtOrder->load('shirt');

        Mail::to($shirtOrder->customer_email)->send(new ShirtOrderReceived($shirtOrder));

        $settings = PaymentGatewaySetting::current();

        if ($settings->hasManualPix()) {
            return redirect()->to(URL::temporarySignedRoute(
                'store.pix.show',
                now()->addDays(7),
                ['shirtOrder' => $shirtOrder],
            ));
        }

        if ($settings->isConfigured()) {
            try {
                $checkout = $this->paymentGateway->createCheckout(new CheckoutRequest(
                    successUrl: route('store.payment.return', ['status' => 'sucesso']),
                    cancelUrl: route('store.payment.return', ['status' => 'cancelado']),
                    expiredUrl: route('store.payment.return', ['status' => 'expirado']),
                    shirtOrder: $shirtOrder,
                ));

                $shirtOrder->update([
                    'payment_gateway' => $checkout->gateway,
                    'payment_gateway_reference' => $checkout->reference,
                    'payment_checkout_url' => $checkout->checkoutUrl,
                ]);

                return redirect()->away($checkout->checkoutUrl);
            } catch (Throwable $exception) {
                report($exception);

                return to_route('store.index')->withErrors([
                    'checkout' => 'O pedido foi registrado, mas não foi possível abrir o checkout. Tente novamente em instantes.',
                ]);
            }
        }

        return to_route('store.index')->with('status', 'Pedido de item avulso registrado com sucesso. O recibo foi enviado por e-mail.');
    }

    public function showPix(ShirtOrder $shirtOrder, PixPayload $pixPayload): View
    {
        $settings = PaymentGatewaySetting::current();
        abort_unless($settings->hasManualPix() && $shirtOrder->participant_registration_id === null, 404);

        $eventSettings = EventSetting::current();
        $payload = $pixPayload->generate(
            key: $settings->pix_key,
            amount: (float) $shirtOrder->total_price,
            receiverName: $settings->pix_account_holder ?: $settings->pix_receiver_name ?: $eventSettings->organizer_legal_name ?: config('app.name'),
            receiverCity: $settings->pix_receiver_city ?: $eventSettings->event_location ?: 'Brasil',
            transactionId: "LOJA{$shirtOrder->id}",
        );

        return view('shirt-order-pix', [
            'shirtOrder' => $shirtOrder->loadMissing('shirt'),
            'pixKey' => $settings->pix_key,
            'pixPayload' => $payload,
            'pixQrCode' => $pixPayload->qrCodeDataUri($payload),
            'pixBank' => $settings->pix_bank,
            'pixAgency' => $settings->pix_agency,
            'pixAccount' => $settings->pix_account,
            'pixAccountHolder' => $settings->pix_account_holder,
        ]);
    }

    public function storePixReceipt(StorePixReceiptRequest $request, ShirtOrder $shirtOrder): RedirectResponse
    {
        abort_unless(
            PaymentGatewaySetting::current()->hasManualPix() && $shirtOrder->participant_registration_id === null,
            404,
        );

        $path = $request->file('pix_receipt')->store('pix-receipts/shirt-orders', 'local');

        $shirtOrder->update([
            'customer_name' => $request->string('billing_name')->toString(),
            'customer_cpf' => $request->string('billing_document')->toString(),
            'pix_receipt_path' => $path,
            'pix_receipt_submitted_at' => now(),
            'payment_status' => 'under_review',
        ]);

        return to_route('store.index')
            ->with('status', 'Comprovante enviado. O pagamento do pedido está em análise.');
    }

    public function paymentReturn(string $status): RedirectResponse
    {
        $message = match ($status) {
            'sucesso' => 'Retorno do checkout recebido. O pedido será confirmado após a conciliação automática.',
            'cancelado' => 'Checkout cancelado. O pedido ficou registrado com pagamento pendente.',
            'expirado' => 'Checkout expirado. O pedido ficou registrado com pagamento pendente.',
        };

        return to_route('store.index')->with('status', $message);
    }
}
