<x-filament-panels::page>
    @php
        $registrations = $this->getRegistrations();
    @endphp

    <style>
        .print-list {
            background: #ffffff;
            color: #18181b;
        }

        .print-list__header {
            align-items: flex-start;
            display: flex;
            gap: 1.5rem;
            justify-content: space-between;
            margin-bottom: 1.5rem;
        }

        .print-list__title {
            font-size: 1.5rem;
            font-weight: 800;
            line-height: 1.2;
            margin: 0;
        }

        .print-list__meta {
            color: #52525b;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        .print-list__count {
            background: #ffffff;
            border: 1px solid #d4d4d8;
            border-radius: 0.375rem;
            font-size: 0.875rem;
            font-weight: 700;
            padding: 0.5rem 0.75rem;
            white-space: nowrap;
        }

        .print-list__table {
            border-collapse: collapse;
            font-size: 0.8125rem;
            width: 100%;
        }

        .print-list__table th,
        .print-list__table td {
            border: 1px solid #d4d4d8;
            padding: 0.45rem 0.55rem;
            text-align: left;
            vertical-align: top;
        }

        .print-list__table th {
            background: #f4f4f5;
            font-weight: 800;
        }

        .print-list__empty {
            border: 1px dashed #a1a1aa;
            border-radius: 0.375rem;
            color: #52525b;
            padding: 2rem;
            text-align: center;
        }

        @media print {
            @page {
                margin: 12mm;
                size: landscape;
            }

            html {
                background: #ffffff !important;
                color-scheme: light !important;
            }

            html::before,
            html::after,
            body::before,
            body::after {
                content: none !important;
                display: none !important;
            }

            body {
                background: #ffffff !important;
                color: #000000 !important;
                color-scheme: light !important;
            }

            body * {
                background: transparent !important;
                box-shadow: none !important;
                opacity: 1 !important;
                text-shadow: none !important;
                visibility: hidden !important;
            }

            .fi-layout,
            .fi-main-ctn,
            .fi-main,
            .fi-page,
            .fi-page-content,
            .print-list {
                background: #ffffff !important;
                color: #000000 !important;
                margin: 0 !important;
                max-width: none !important;
                padding: 0 !important;
            }

            .print-list {
                display: block !important;
                left: 0 !important;
                position: absolute !important;
                top: 0 !important;
                visibility: visible !important;
                width: 100% !important;
                z-index: 2147483647 !important;
            }

            .print-list *,
            .print-list__meta,
            .print-list__empty {
                background: transparent !important;
                box-shadow: none !important;
                color: #000000 !important;
                text-shadow: none !important;
                visibility: visible !important;
            }

            .print-list__table {
                font-size: 10px;
            }

            .print-list__table th,
            .print-list__table td {
                background: #ffffff !important;
                border-color: #737373 !important;
            }

            .print-list__table tr {
                break-inside: avoid;
            }
        }
    </style>

    <section class="print-list">
        <header class="print-list__header">
            <div>
                <h2 class="print-list__title">Lista de inscricoes</h2>
                <p class="print-list__meta">Gerada em {{ now()->format('d/m/Y H:i') }}</p>
            </div>

            <div class="print-list__count">
                {{ $registrations->count() }} inscricoes
            </div>
        </header>

        @if ($registrations->isEmpty())
            <div class="print-list__empty">
                Nenhuma inscricao encontrada.
            </div>
        @else
            <table class="print-list__table">
                <thead>
                    <tr>
                        <th>Atleta</th>
                        <th>Nascimento</th>
                        <th>CPF participante</th>
                        <th>Responsavel</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th>Modalidade</th>
                        <th>Pagamento</th>
                        <th>Inscrito em</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($registrations as $registration)
                        <tr>
                            <td>{{ $registration->athlete_name }}</td>
                            <td>{{ $registration->birth_date?->format('d/m/Y') }}</td>
                            <td>{{ $registration->participant_cpf }}</td>
                            <td>{{ $registration->guardian_name ?: '-' }}</td>
                            <td>{{ $registration->phone }}</td>
                            <td>{{ $registration->email }}</td>
                            <td>{{ $registration->modality }}</td>
                            <td>{{ $registration->paymentStatusLabel() }}</td>
                            <td>{{ $registration->created_at?->format('d/m/Y H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </section>

    <script>
        window.addEventListener('load', () => window.print());
    </script>
</x-filament-panels::page>
