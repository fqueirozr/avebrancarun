<?php

use App\Actions\CreateShirtOrder;
use App\Filament\Resources\ShirtOrders\Pages\EditShirtOrder;
use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Mail\ShirtOrderReceived;
use App\Mail\ShirtOrderUpdated;
use App\Models\ParticipantRegistration;
use App\Models\PaymentGatewaySetting;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use App\Models\User;
use App\Payments\CheckoutRequest;
use App\Payments\CheckoutResponse;
use App\Payments\PaymentGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('shows a shirt photo from the public disk in the store', function () {
    $shirt = Shirt::factory()->create([
        'name' => 'Camiseta Oficial',
        'photo_path' => 'shirts/camiseta-oficial.png',
    ]);

    $this->get(route('store.index'))
        ->assertSuccessful()
        ->assertSee(
            'src="'.Storage::disk('public')->url($shirt->photo_path).'"',
            false,
        )
        ->assertSee('alt="Foto de Camiseta Oficial"', false);
});

it('updates linked shirt payments when the registration payment changes', function () {
    $registration = ParticipantRegistration::factory()->create(['payment_status' => 'pending']);
    $shirt = Shirt::factory()->create();

    $linkedOrder = ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'participant_registration_id' => $registration->id,
        'customer_name' => 'Maria Vinculada',
        'customer_email' => 'vinculada@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);
    $standaloneOrder = ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'João Avulso',
        'customer_email' => 'avulso@example.com',
        'customer_phone' => '11888888888',
        'size' => 'G',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    $registration->update(['payment_status' => 'paid']);

    expect($linkedOrder->refresh()->payment_status)->toBe('paid')
        ->and($standaloneOrder->refresh()->payment_status)->toBe('pending');
});

it('allows an admin to update a standalone shirt payment', function () {
    config(['app.env' => 'local']);
    Mail::fake();

    $user = User::factory()->create();
    $shirt = Shirt::factory()->create();
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'João Avulso',
        'customer_email' => 'avulso@example.com',
        'customer_phone' => '11888888888',
        'size' => 'G',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($user);

    Livewire::test(EditShirtOrder::class, ['record' => $shirtOrder->getRouteKey()])
        ->fillForm(['payment_status' => 'paid'])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($shirtOrder->refresh()->payment_status)->toBe('paid');

    Mail::assertQueued(ShirtOrderUpdated::class, function (ShirtOrderUpdated $mail): bool {
        return $mail->hasTo('avulso@example.com')
            && $mail->shirtOrder->payment_status === 'paid';
    });
});

it('does not email a payment update when the standalone shirt status is unchanged', function () {
    config(['app.env' => 'local']);
    Mail::fake();

    $this->actingAs(User::factory()->create());

    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'customer_name' => 'João Avulso',
        'customer_email' => 'avulso@example.com',
        'customer_phone' => '11888888888',
        'size' => 'G',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    Livewire::test(EditShirtOrder::class, ['record' => $shirtOrder->getRouteKey()])
        ->call('save')
        ->assertHasNoFormErrors();

    Mail::assertNotQueued(ShirtOrderUpdated::class);
});

it('renders the standalone shirt payment update email', function () {
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(['name' => 'Camiseta Oficial']),
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'sizes' => ['M', 'G'],
        'quantity' => 2,
        'unit_price' => 35,
        'total_price' => 70,
        'payment_status' => 'paid',
    ]);

    $mail = new ShirtOrderUpdated($shirtOrder->load('shirt'));

    $mail->assertHasSubject('Item avulso atualizado - Ave Branca Run');
    $mail->assertSeeInHtml('Camiseta Oficial');
    $mail->assertSeeInHtml('R$ 70,00');
    $mail->assertSeeInHtml('Pago');
});

it('shows the linked registration payment receipt in the shirt form', function () {
    config(['app.env' => 'local']);
    Storage::fake('local');

    $receiptPath = 'pix-receipts/comprovante.png';
    Storage::disk('local')->put($receiptPath, 'receipt');

    $user = User::factory()->create();
    $registration = ParticipantRegistration::factory()->create([
        'pix_receipt_path' => $receiptPath,
    ]);
    $shirt = Shirt::factory()->create();
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'participant_registration_id' => $registration->id,
        'customer_name' => 'Maria Vinculada',
        'customer_email' => 'vinculada@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
    ]);

    $this->actingAs($user);

    Livewire::test(EditShirtOrder::class, ['record' => $shirtOrder->getRouteKey()])
        ->assertSchemaComponentVisible('payment_receipt_path')
        ->assertSchemaStateSet(['payment_receipt_path' => $receiptPath]);
});

it('registers a standalone shirt order and decrements stock', function () {
    Mail::fake();

    $shirt = Shirt::factory()->create(['stock_quantity' => 5, 'price' => 35]);

    $this->post(route('store.store'), [
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '529.982.247-25',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'sizes' => ['M', 'G'],
        'quantity' => 2,
    ])->assertRedirect(route('store.index'));

    $shirtOrder = ShirtOrder::query()->whereBelongsTo($shirt)->firstOrFail();

    expect($shirtOrder->customer_cpf)->toBe('52998224725')
        ->and($shirtOrder->sizes)->toBe(['M', 'G'])
        ->and($shirtOrder->quantity)->toBe(2)
        ->and((float) $shirtOrder->total_price)->toBe(70.0);
    expect($shirt->refresh()->stock_quantity)->toBe(3);

    Mail::assertSent(ShirtOrderReceived::class, 'maria@example.com');
});

it('redirects a standalone order to the manual pix receipt page', function () {
    Mail::fake();

    PaymentGatewaySetting::factory()->create([
        'is_enabled' => false,
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);
    $shirt = Shirt::factory()->create(['price' => 35]);

    $response = $this->post(route('store.store'), [
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '52998224725',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'sizes' => ['M'],
        'quantity' => 1,
    ]);

    $shirtOrder = ShirtOrder::query()->whereBelongsTo($shirt)->firstOrFail();

    $response->assertRedirectContains("/loja/pedido/{$shirtOrder->id}/pix");

    $this->get($response->headers->get('Location'))
        ->assertSuccessful()
        ->assertSee('Conclua seu pedido')
        ->assertSee('financeiro@example.com');
});

it('redirects a standalone order to the active payment gateway checkout', function () {
    Mail::fake();

    PaymentGatewaySetting::factory()->create([
        'is_enabled' => true,
        'manual_pix_enabled' => false,
        'api_key' => 'test-key',
    ]);
    $this->app->bind(PaymentGateway::class, fn (): PaymentGateway => new class implements PaymentGateway
    {
        public function createCheckout(CheckoutRequest $request): CheckoutResponse
        {
            expect($request->shirtOrder)->toBeInstanceOf(ShirtOrder::class);

            return new CheckoutResponse(
                gateway: 'fake',
                reference: 'shirt_checkout_123',
                checkoutUrl: 'https://checkout.example/shirt_checkout_123',
            );
        }
    });
    $shirt = Shirt::factory()->create(['price' => 35]);

    $this->post(route('store.store'), [
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '52998224725',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'sizes' => ['M'],
        'quantity' => 1,
    ])->assertRedirect('https://checkout.example/shirt_checkout_123');

    $shirtOrder = ShirtOrder::query()->whereBelongsTo($shirt)->firstOrFail();

    expect($shirtOrder->payment_gateway)->toBe('fake')
        ->and($shirtOrder->payment_gateway_reference)->toBe('shirt_checkout_123')
        ->and($shirtOrder->payment_checkout_url)->toBe('https://checkout.example/shirt_checkout_123');
});

it('stores a manual pix receipt for a standalone order', function () {
    Storage::fake('local');

    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'participant_registration_id' => null,
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '52998224725',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);
    $url = URL::temporarySignedRoute(
        'store.pix.store',
        now()->addHour(),
        ['shirtOrder' => $shirtOrder],
    );

    $this->post($url, [
        'billing_name' => 'Maria Silva',
        'billing_document' => '52998224725',
        'pix_receipt' => UploadedFile::fake()->image('comprovante.png'),
        'payer_data_confirmed' => '1',
    ])->assertRedirect(route('store.index'));

    $shirtOrder->refresh();

    expect($shirtOrder->payment_status)->toBe('under_review')
        ->and($shirtOrder->pix_receipt_submitted_at)->not->toBeNull();
    Storage::disk('local')->assertExists($shirtOrder->pix_receipt_path);
});

it('shows the customer cpf in the standalone order admin form', function () {
    $this->actingAs(User::factory()->create());

    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '52998224725',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    Livewire::test(EditShirtOrder::class, ['record' => $shirtOrder->getRouteKey()])
        ->assertSchemaStateSet(['customer_cpf' => '52998224725']);
});

it('requires cpf and every shirt size for a standalone store order', function () {
    $shirt = Shirt::factory()->create();

    $this->post(route('store.store'), [
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'quantity' => 1,
    ])->assertSessionHasErrors(['customer_cpf', 'sizes']);
});

it('requires one size for each standalone shirt', function () {
    $shirt = Shirt::factory()->create();

    $this->post(route('store.store'), [
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '52998224725',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'sizes' => ['M'],
        'quantity' => 2,
    ])->assertSessionHasErrors('sizes');
});

it('uses the discounted item price when purchased with a registration', function () {
    $registration = ParticipantRegistration::factory()->create();
    $shirt = Shirt::factory()->create([
        'price' => 50,
        'registration_price' => 35,
    ]);

    $order = app(CreateShirtOrder::class)->handle($shirt, [
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 2,
    ], $registration);

    expect((float) $order->unit_price)->toBe(35.0)
        ->and((float) $order->total_price)->toBe(70.0);
});

it('adds the configured surcharge for each large standalone shirt', function () {
    $shirt = Shirt::factory()->create([
        'price' => 35,
        'size_2xl_surcharge' => 10,
        'size_3xl_surcharge' => 15,
    ]);

    $order = app(CreateShirtOrder::class)->handle($shirt, [
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'sizes' => ['M', '2XG', '3XG'],
        'quantity' => 3,
    ]);

    expect((float) $order->unit_price)->toBe(35.0)
        ->and((float) $order->total_price)->toBe(130.0);
});

it('rejects a pix receipt larger than five megabytes', function () {
    PaymentGatewaySetting::factory()->create([
        'manual_pix_enabled' => true,
        'pix_key' => 'financeiro@example.com',
    ]);
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'participant_registration_id' => null,
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
    ]);
    $url = URL::temporarySignedRoute('store.pix.store', now()->addHour(), ['shirtOrder' => $shirtOrder]);

    $this->post($url, [
        'billing_name' => 'Maria Silva',
        'billing_document' => '52998224725',
        'pix_receipt' => UploadedFile::fake()->create('comprovante.pdf', 5121, 'application/pdf'),
        'payer_data_confirmed' => '1',
    ])->assertSessionHasErrors('pix_receipt');
});

it('renders the standalone shirt order as a receipt', function () {
    $shirt = Shirt::factory()->create(['name' => 'Camiseta Oficial', 'price' => 35]);
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 2,
        'unit_price' => 35,
        'total_price' => 70,
    ]);

    $mail = new ShirtOrderReceived($shirtOrder->load('shirt'));

    $mail->assertHasSubject('Pedido de item avulso recebido - Ave Branca Run');
    $mail->assertSeeInHtml('Camiseta Oficial');
    $mail->assertSeeInHtml('R$ 70,00');
    $mail->assertSeeInHtml('Pendente');
    $mail->assertSeeInHtml('serve como recibo do pedido');
});

it('allows an authenticated admin to print the standalone shirt delivery list', function () {
    config(['app.env' => 'local']);

    $user = User::factory()->create();
    $shirt = Shirt::factory()->create(['name' => 'Camiseta Oficial']);

    ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_cpf' => '52998224725',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 2,
        'unit_price' => 35,
        'total_price' => 70,
        'payment_status' => 'paid',
    ]);

    ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'João Pendente',
        'customer_email' => 'joao@example.com',
        'customer_phone' => '11888888888',
        'size' => 'G',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    $this->actingAs($user)
        ->get(ShirtOrderResource::getUrl('print'))
        ->assertSuccessful()
        ->assertSee('Lista de entrega de camisetas avulsas')
        ->assertSee('Maria Silva')
        ->assertSee('52998224725')
        ->assertSee('Camiseta Oficial')
        ->assertSee('Assinatura do recebedor')
        ->assertDontSee('maria@example.com')
        ->assertDontSee('11999999999')
        ->assertSee('João Pendente')
        ->assertSee('Pendente');
});
