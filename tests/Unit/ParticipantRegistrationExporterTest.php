<?php

use App\Filament\Exports\ParticipantRegistrationExporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;

test('participant export contains only the approved operational columns', function () {
    $columnNames = collect(ParticipantRegistrationExporter::getColumns())
        ->map(fn ($column): string => $column->getName())
        ->all();

    expect($columnNames)->toBe([
        'protocol_number',
        'athlete_name',
        'birth_date',
        'sex',
        'modality',
        'kit.name',
        'bib_number',
        'payment_status',
        'created_at',
    ])->not->toContain(
        'participant_cpf',
        'guardian_cpf',
        'phone',
        'email',
        'health_notes',
        'payment_gateway_reference',
    );
});

test('participant export generates an Excel file', function () {
    $exporter = new ParticipantRegistrationExporter(new Export, [], []);

    expect($exporter->getFormats())->toBe([ExportFormat::Xlsx]);
});
