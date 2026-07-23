<?php

use App\Filament\Exports\ParticipantRegistrationExporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;

test('participant export contains personal contact and standalone item fields', function () {
    $columnNames = collect(ParticipantRegistrationExporter::getColumns())
        ->map(fn ($column): string => $column->getName())
        ->all();

    expect($columnNames)->toContain(
        'participant_cpf',
        'guardian_cpf',
        'phone',
        'email',
        'standalone_items',
        'payment_gateway_reference',
    );
});

test('participant export generates an Excel file', function () {
    $exporter = new ParticipantRegistrationExporter(new Export, [], []);

    expect($exporter->getFormats())->toBe([ExportFormat::Xlsx]);
});
