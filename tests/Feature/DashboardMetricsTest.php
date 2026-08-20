<?php

use App\Filament\Widgets\RegistrationOverview;
use App\Filament\Widgets\ShirtSizeOverview;
use App\Models\Kit;
use App\Models\ParticipantRegistration;
use App\Models\Shirt;
use App\Models\ShirtOrder;
use App\Models\User;
use App\Support\DashboardMetrics;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('the admin dashboard displays registration revenue and shirt widgets', function () {
    $this->actingAs(User::factory()->create());

    $this
        ->get('/admin')
        ->assertSuccessful()
        ->assertSeeLivewire(RegistrationOverview::class)
        ->assertSeeLivewire(ShirtSizeOverview::class);

    Livewire::test(RegistrationOverview::class)
        ->assertOk()
        ->assertSee('Inscrições e arrecadação')
        ->assertSee('Valor arrecadado');

    Livewire::test(ShirtSizeOverview::class)
        ->assertOk()
        ->assertSee('Camisetas por tamanho');
});

test('it counts registrations by payment status', function () {
    ParticipantRegistration::factory()->paid()->create();
    ParticipantRegistration::factory()->create(['payment_status' => 'under_review']);
    ParticipantRegistration::factory()->create(['payment_status' => 'pending']);
    ParticipantRegistration::factory()->cancelled()->create();

    expect(app(DashboardMetrics::class)->registrationCounts())->toBe([
        'paid' => 1,
        'under_review' => 1,
        'pending' => 1,
        'cancelled' => 1,
    ]);
});

test('it totals revenue from paid registrations and shirt orders without double counting', function () {
    $kit = Kit::factory()->create([
        'price' => 100,
        'has_shirt' => true,
        'size_2xl_surcharge' => 10,
    ]);
    $registration = ParticipantRegistration::factory()->paid()->create([
        'kit_id' => $kit->id,
        'shirt_size' => '2XG',
    ]);
    ParticipantRegistration::factory()->create(['payment_status' => 'pending']);
    $shirt = Shirt::factory()->create();

    ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'participant_registration_id' => $registration->id,
        'customer_name' => 'Atleta',
        'customer_email' => 'atleta@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'quantity' => 1,
        'unit_price' => 30,
        'total_price' => 30,
        'payment_status' => 'paid',
    ]);
    ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'Cliente',
        'customer_email' => 'cliente@example.com',
        'customer_phone' => '11999999998',
        'size' => 'G',
        'quantity' => 1,
        'unit_price' => 40,
        'total_price' => 40,
        'payment_status' => 'paid',
    ]);

    expect(app(DashboardMetrics::class)->collectedRevenue())->toBe(180.0);
});

test('it counts included and standalone shirts by size and ignores cancelled records', function () {
    $kitWithShirt = Kit::factory()->create(['has_shirt' => true]);
    $kitWithoutShirt = Kit::factory()->create(['has_shirt' => false]);

    ParticipantRegistration::factory()->create(['kit_id' => $kitWithShirt->id, 'shirt_size' => 'M']);
    ParticipantRegistration::factory()->create(['kit_id' => $kitWithoutShirt->id, 'shirt_size' => 'M']);
    ParticipantRegistration::factory()->cancelled()->create(['kit_id' => $kitWithShirt->id, 'shirt_size' => 'M']);

    $shirt = Shirt::factory()->create();
    ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'Cliente',
        'customer_email' => 'cliente@example.com',
        'customer_phone' => '11999999999',
        'size' => 'M',
        'sizes' => ['M', 'G', 'G'],
        'quantity' => 3,
        'unit_price' => 30,
        'total_price' => 90,
        'payment_status' => 'pending',
    ]);
    ShirtOrder::factory()->create([
        'shirt_id' => $shirt->id,
        'customer_name' => 'Cancelado',
        'customer_email' => 'cancelado@example.com',
        'customer_phone' => '11999999998',
        'size' => 'M',
        'quantity' => 2,
        'unit_price' => 30,
        'total_price' => 60,
        'payment_status' => 'cancelled',
    ]);

    $counts = app(DashboardMetrics::class)->shirtCountsBySize();

    expect($counts['M'])->toBe(['included' => 1, 'standalone' => 1, 'total' => 2])
        ->and($counts['G'])->toBe(['included' => 0, 'standalone' => 2, 'total' => 2]);
});
