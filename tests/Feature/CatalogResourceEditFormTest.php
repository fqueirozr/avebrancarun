<?php

use App\Filament\Resources\Kits\Pages\EditKit;
use App\Filament\Resources\Shirts\Pages\EditShirt;
use App\Models\Kit;
use App\Models\Shirt;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

it('organizes the package edit form into sections', function () {
    $this->actingAs(User::factory()->create());
    $kit = Kit::factory()->create();

    Livewire::test(EditKit::class, ['record' => $kit->getRouteKey()])
        ->assertOk()
        ->assertSchemaComponentExists('kit-presentation')
        ->assertSchemaComponentExists('kit-pricing')
        ->assertSchemaComponentExists('kit-availability')
        ->assertSchemaComponentExists('kit-rules')
        ->assertSchemaStateSet([
            'name' => $kit->name,
            'price' => $kit->price,
            'is_active' => $kit->is_active,
        ]);
});

it('organizes the standalone item edit form into sections', function () {
    $this->actingAs(User::factory()->create());
    $shirt = Shirt::factory()->create();

    Livewire::test(EditShirt::class, ['record' => $shirt->getRouteKey()])
        ->assertOk()
        ->assertSchemaComponentExists('shirt-presentation')
        ->assertSchemaComponentExists('shirt-pricing')
        ->assertSchemaComponentExists('shirt-availability')
        ->assertSchemaStateSet([
            'name' => $shirt->name,
            'price' => $shirt->price,
            'is_active' => $shirt->is_active,
        ]);
});
