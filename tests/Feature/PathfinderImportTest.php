<?php

use App\Actions\ImportPathfinders;
use App\Models\ParticipantRegistration;
use App\Models\Pathfinder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

uses(RefreshDatabase::class);

it('imports pathfinders from an Excel spreadsheet', function () {
    $path = tempnam(sys_get_temp_dir(), 'pathfinders').'.xlsx';
    $writer = new Writer;
    $writer->openToFile($path);
    $writer->addRow(Row::fromValues(['Nome', 'CPF']));
    $writer->addRow(Row::fromValues(['Maria Silva', '153.509.460-56']));
    $writer->addRow(Row::fromValues(['João Souza', '529.982.247-25']));
    $writer->close();

    expect(app(ImportPathfinders::class)->handle($path))->toBe(2);
    $this->assertDatabaseHas('pathfinders', ['name' => 'Maria Silva', 'cpf' => '15350946056']);
    $this->assertDatabaseHas('pathfinders', ['name' => 'João Souza', 'cpf' => '52998224725']);

    unlink($path);
});

it('reports whether a cpf may use the pathfinder package', function () {
    $pathfinder = Pathfinder::factory()->create(['cpf' => '15350946056']);

    $this->postJson(route('registration.pathfinder.check'), ['cpf' => '153.509.460-56'])
        ->assertSuccessful()
        ->assertJson(['eligible' => true]);

    ParticipantRegistration::factory()->create(['pathfinder_id' => $pathfinder->id]);

    $this->postJson(route('registration.pathfinder.check'), ['cpf' => '15350946056'])
        ->assertSuccessful()
        ->assertJson(['eligible' => false]);
});
