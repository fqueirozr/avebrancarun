<?php

namespace App\Filament\Exports;

use App\Models\ShirtOrder;
use Filament\Actions\Exports\Enums\ExportFormat;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Number;

class ShirtOrderExporter extends Exporter
{
    protected static ?string $model = ShirtOrder::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')->label('Pedido'),
            ExportColumn::make('participantRegistration.protocol_number')->label('Inscrição vinculada'),
            ExportColumn::make('shirt.name')
                ->label('Item avulso')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('customer_name')
                ->label('Cliente')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('customer_cpf')->label('CPF'),
            ExportColumn::make('customer_email')
                ->label('E-mail')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('customer_phone')->label('Telefone'),
            ExportColumn::make('size_summary')
                ->label('Tamanhos')
                ->state(fn (ShirtOrder $record): string => $record->sizeSummary()),
            ExportColumn::make('quantity')->label('Quantidade'),
            ExportColumn::make('unit_price')->label('Valor unitário'),
            ExportColumn::make('total_price')->label('Valor total'),
            ExportColumn::make('payment_status')
                ->label('Pagamento')
                ->formatStateUsing(fn (?string $state): string => ShirtOrder::paymentStatusOptions()[$state] ?? 'Pendente'),
            ExportColumn::make('payment_gateway')->label('Meio de pagamento'),
            ExportColumn::make('payment_gateway_reference')->label('Referência do pagamento'),
            ExportColumn::make('created_at')->label('Pedido em'),
        ];
    }

    public static function modifyQuery(Builder $query): Builder
    {
        return $query->with(['shirt:id,name', 'participantRegistration:id,protocol_number']);
    }

    public function getFormats(): array
    {
        return [ExportFormat::Xlsx];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'A exportação da lista de pedidos avulsos foi concluída: '.Number::format($export->successful_rows).' '.str('registro')->plural($export->successful_rows).' exportado(s).';

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
