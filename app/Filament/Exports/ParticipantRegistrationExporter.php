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
            ExportColumn::make('participant_cpf')
                ->label('CPF do atleta'),
            ExportColumn::make('shirt_size')
                ->label('Tamanho da camisa'),
            ExportColumn::make('birth_date')
                ->label('Data de nascimento'),
            ExportColumn::make('sex')
                ->label('Sexo')
                ->formatStateUsing(fn (?string $state): string => ParticipantRegistration::sexOptions()[$state] ?? 'Não informado'),
            ExportColumn::make('guardian_name')
                ->label('Responsável legal')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('guardian_cpf')
                ->label('CPF do responsável'),
            ExportColumn::make('filled_by_legal_representative')
                ->label('Preenchida pelo responsável')
                ->formatStateUsing(fn (bool $state): string => $state ? 'Sim' : 'Não'),
            ExportColumn::make('phone')
                ->label('Telefone'),
            ExportColumn::make('email')
                ->label('E-mail')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('billing_name')
                ->label('Nome do pagador')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('billing_document')
                ->label('CPF/CNPJ do pagador'),
            ExportColumn::make('billing_address')
                ->label('Endereço do pagador')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('billing_address_number')
                ->label('Número do endereço'),
            ExportColumn::make('billing_province')
                ->label('Bairro'),
            ExportColumn::make('billing_postal_code')
                ->label('CEP'),
            ExportColumn::make('modality')
                ->label('Prova')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('kit.name')
                ->label('Pacote')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('pathfinder.name')
                ->label('Desbravador')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('standalone_items')
                ->label('Itens avulsos')
                ->state(fn (ParticipantRegistration $record): string => $record->shirtOrders
                    ->map(fn ($order): string => ($order->shirt?->name ?? 'Item').' ('.$order->size.') × '.$order->quantity.' — R$ '.number_format((float) $order->total_price, 2, ',', '.'))
                    ->join('; '))
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('registration_total')
                ->label('Valor total')
                ->state(fn (ParticipantRegistration $record): float => $record->kit === null ? 0 : $record->priceFor($record->kit)),
            ExportColumn::make('bib_number')
                ->label('Número de peito')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('payment_status')
                ->label('Pagamento')
                ->formatStateUsing(fn (?string $state): string => ParticipantRegistration::paymentStatusOptions()[$state] ?? 'Pendente'),
            ExportColumn::make('payment_gateway')
                ->label('Meio de pagamento'),
            ExportColumn::make('payment_gateway_reference')
                ->label('Referência do pagamento'),
            ExportColumn::make('emergency_contact_name')
                ->label('Contato de emergência')
                ->formatStateUsing(fn (?string $state): string => self::safeSpreadsheetText($state)),
            ExportColumn::make('emergency_contact_phone')
                ->label('Telefone de emergência'),
            ExportColumn::make('result_status')
                ->label('Status na prova')
                ->formatStateUsing(fn (?string $state): string => ParticipantRegistration::resultStatusOptions()[$state] ?? ''),
            ExportColumn::make('elapsed_time')
                ->label('Tempo oficial'),
            ExportColumn::make('result_category')
                ->label('Categoria'),
            ExportColumn::make('overall_rank')
                ->label('Classificação geral'),
            ExportColumn::make('sex_rank')
                ->label('Classificação por sexo'),
            ExportColumn::make('category_rank')
                ->label('Classificação na categoria'),
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
