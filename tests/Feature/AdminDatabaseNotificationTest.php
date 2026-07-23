<?php

use App\Filament\Resources\ParticipantRegistrations\ParticipantRegistrationResource;
use App\Filament\Resources\ShirtOrders\ShirtOrderResource;
use App\Models\ParticipantRegistration;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('notifies every admin about a new registration', function () {
    $admins = User::factory()->count(2)->create();
    $registration = ParticipantRegistration::factory()->create([
        'athlete_name' => 'Maria Silva',
    ]);

    $admins->each(function (User $admin) use ($registration): void {
        $notification = $admin->notifications()->sole();

        expect($notification->data)
            ->title->toBe('Nova inscrição recebida')
            ->body->toContain('Maria Silva')
            ->actions->toHaveCount(1)
            ->and($notification->data['actions'][0]['url'])
            ->toBe(ParticipantRegistrationResource::getUrl('edit', ['record' => $registration]));
    });
});

it('notifies every admin about a standalone shirt order', function () {
    $admin = User::factory()->create();
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'participant_registration_id' => null,
        'customer_name' => 'João Comprador',
        'customer_email' => 'joao@example.com',
        'customer_phone' => '11999999999',
        'size' => 'G',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    $notification = $admin->notifications()->sole();

    expect($notification->data)
        ->title->toBe('Novo pedido de item avulso')
        ->body->toContain('João Comprador')
        ->actions->toHaveCount(1)
        ->and($notification->data['actions'][0]['url'])
        ->toBe(ShirtOrderResource::getUrl('edit', ['record' => $shirtOrder]));
});

it('does not duplicate the notification for a shirt linked to a registration', function () {
    $registration = ParticipantRegistration::factory()->create();
    $admin = User::factory()->create();
    $shirtOrder = ShirtOrder::factory()->create([
        'shirt_id' => Shirt::factory(),
        'participant_registration_id' => $registration,
        'customer_name' => 'Maria Atleta',
        'customer_email' => 'maria@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 35,
        'total_price' => 35,
        'payment_status' => 'pending',
    ]);

    expect($admin->notifications()->count())->toBe(0);
});
