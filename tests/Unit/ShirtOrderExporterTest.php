<?php

use App\Filament\Exports\ShirtOrderExporter;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\Models\Export;

test('it exports the standalone order fields as xlsx', function () {
    $columnNames = collect(ShirtOrderExporter::getColumns())
        ->map->getName()
        ->all();

    expect($columnNames)->toContain(
        'participantRegistration.protocol_number',
        'shirt.name',
        'customer_name',
        'size_summary',
        'quantity',
        'total_price',
        'payment_status',
    );

    $exporter = new ShirtOrderExporter(new Export, [], []);

    expect($exporter->getFormats())->toBe([ExportFormat::Xlsx]);
});
