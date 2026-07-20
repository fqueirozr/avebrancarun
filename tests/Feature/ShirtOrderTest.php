<?php

use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('registers a standalone shirt order and decrements stock', function () {
    $shirt = Shirt::factory()->create(['stock_quantity' => 5, 'price' => 35]);

    $this->post(route('shirts.store'), [
        'shirt_id' => $shirt->id,
        'customer_name' => 'Maria Silva',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 2,
    ])->assertRedirect(route('shirts.index'));

    $this->assertDatabaseHas('shirt_orders', ['shirt_id' => $shirt->id, 'quantity' => 2, 'total_price' => 70]);
    expect($shirt->refresh()->stock_quantity)->toBe(3);
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
