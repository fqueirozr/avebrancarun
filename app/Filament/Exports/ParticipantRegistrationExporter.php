<?php

namespace App\Filament\Exports;

use App\Models\ParticipantRegistration;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Support\Number;

class ParticipantRegistrationExporter extends Exporter
{
    protected static ?string $model = ParticipantRegistration::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('protocol_number')
                ->label('Protocolo'),
            ExportColumn::make('athlete_name')
                ->label('Atleta')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('birth_date')
                ->label('Data de nascimento'),
            ExportColumn::make('sex')
                ->label('Sexo')
                ->formatStateUsing(fn (?string $state): string => ParticipantRegistration::sexOptions()[$state] ?? 'Não informado'),
            ExportColumn::make('modality')
                ->label('Prova')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('kit.name')
                ->label('Kit')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('bib_number')
                ->label('Número de peito')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('payment_status')
                ->label('Pagamento')
                ->formatStateUsing(fn (?string $state): string => ParticipantRegistration::paymentStatusOptions()[$state] ?? 'Pendente'),
            ExportColumn::make('created_at')
                ->label('Inscrito em'),
        ];
    }

    public function getFormats(): array
    {
        return [ExportFormat::Xlsx];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação da lista de inscritos foi concluída: '.Number::format($export->successful_rows).' '.str('registro')->plural($export->successful_rows).' exportado(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.Number::format($failedRowsCount).' registro(s) não puderam ser exportados.';
        }

        return $body;
    }

    private static function safeSpreadsheetText(?string $value): string
    {
        if (blank($value)) {
            return '';
        }

        return str($value)->startsWith(['=', '+', '-', '@']) ? "'{$value}" : $value;
    }
}
