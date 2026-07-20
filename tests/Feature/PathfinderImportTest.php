<?php

use App\Actions\ImportPathfinders;
use Illuminate\Foundation\Testing\RefreshDatabase;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;

uses(RefreshDatabase::class);

it('imports pathfinders from an Excel spreadsheet', function () {
    $path = tempnam(sys_get_temp_dir(), 'pathfinders').'.xlsx';
    $writer = new Writer;
    $writer->openToFile($path);
    $writer->addRow(Row::fromValues(['Nome']));
    $writer->addRow(Row::fromValues(['Maria Silva']));
    $writer->addRow(Row::fromValues(['João Souza']));
    $writer->close();

    expect(app(ImportPathfinders::class)->handle($path))->toBe(2);
    $this->assertDatabaseHas('pathfinders', ['name' => 'Maria Silva']);
    $this->assertDatabaseHas('pathfinders', ['name' => 'João Souza']);

    unlink($path);
});
