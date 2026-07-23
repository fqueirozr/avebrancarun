<?php

use App\Actions\CreateShirtOrder;
use App\Filament\Resources\ShirtOrders\Pages\EditShirtOrder;
use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Mail\ShirtOrderReceived;
use App\Mail\ShirtOrderUpdated;
use App\Models\ParticipantRegistration;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

uses(RefreshDatabase::class);

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
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 2,
    ])->assertRedirect(route('store.index'));

    $this->assertDatabaseHas('shirt_orders', ['shirt_id' => $shirt->id, 'quantity' => 2, 'total_price' => 70]);
    expect($shirt->refresh()->stock_quantity)->toBe(3);

    Mail::assertSent(ShirtOrderReceived::class, 'maria@example.com');
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
        ->assertSee('Camiseta Oficial')
        ->assertSee('Assinatura do recebedor')
        ->assertDontSee('maria@example.com')
        ->assertDontSee('11999999999')
        ->assertSee('João Pendente')
        ->assertSee('Pendente');
});
