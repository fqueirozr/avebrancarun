<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Página de inscrição da Ave Branca Run.">

        <title>Inscrição | Ave Branca Run</title>

        <link rel="icon" href="{{ asset('images/favicon-60-anos.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('images/favicon-60-anos.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#f7fbff] text-zinc-950 antialiased">
        <header class="sticky top-0 z-50 border-b border-white/10 bg-race-night/95 text-white shadow-lg shadow-race-night/20 backdrop-blur">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-3 sm:px-8 sm:py-4" aria-label="Navegação principal">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3 rounded-md outline-none transition focus-visible:ring-3 focus-visible:ring-race-cyan/35" aria-label="Ir para a página inicial da Ave Branca Run">
                    <img
                        src="{{ asset('images/ave-branca-run-logo.png') }}"
                        alt="Ave Branca Run"
                        class="h-11 w-auto max-w-40 shrink-0 object-contain sm:h-14 sm:max-w-[220px]"
                    >

                    <span class="hidden min-w-0 border-l border-white/15 pl-3 sm:grid">
                        <span class="text-xs font-black uppercase tracking-wide text-race-cyan">Inscrição 2026</span>
                        <span class="truncate text-sm font-semibold text-white/60">Formulário do atleta</span>
                    </span>
                </a>

                <div class="flex shrink-0 items-center gap-3">
                    <span class="hidden items-center gap-2 text-xs font-bold text-white/65 md:flex">
                        <span class="size-2 rounded-full bg-emerald-400 ring-4 ring-emerald-400/15" aria-hidden="true"></span>
                        Ambiente seguro
                    </span>

                    <a href="{{ route('home') }}" class="inline-flex min-h-10 items-center justify-center gap-2 rounded-md border border-white/15 bg-white/5 px-3 py-2 text-sm font-black text-white shadow-sm transition hover:border-race-cyan hover:bg-race-cyan hover:text-race-night focus-visible:outline-none focus-visible:ring-3 focus-visible:ring-race-cyan/35 sm:px-4">
                        <svg class="size-4" viewBox="0 0 20 20" fill="none" aria-hidden="true">
                            <path d="M12.5 15 7.5 10l5-5" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                        <span class="hidden sm:inline">Voltar ao site</span>
                        <span class="sm:hidden">Voltar</span>
                    </a>
                </div>
            </nav>
        </header>

        <main class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-10 sm:px-8 lg:grid-cols-[0.75fr_1.25fr] lg:py-14">
            <aside class="lg:sticky lg:top-8 lg:self-start">
                <div class="overflow-hidden rounded-md bg-race-night text-white shadow-xl shadow-amber-950/20">
                    <img
                        src="{{ asset('images/ave-branca-run-2026-hero-client.png') }}"
                        alt="Arte da Ave Branca Run com informações da corrida"
                        class="h-auto w-full bg-race-night object-contain"
                    >
                    <div class="p-6">
                        <p class="text-sm font-semibold text-race-cyan">Inscrição da corrida</p>
                        <h1 class="mt-3 text-3xl font-black leading-tight">Ave Branca Run</h1>
                        <p class="mt-4 text-sm leading-6 text-white/78">
                            Preencha os dados do atleta, escolha a prova e conclua o pagamento por Pix.
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-md border border-amber-200 bg-amber-50 p-5 text-amber-950">
                    <p class="font-black">Pagamento por Pix</p>
                    <p class="mt-2 text-sm leading-6">
                        Após a inscrição, você verá a chave Pix e poderá enviar o comprovante para análise.
                    </p>
                </div>
            </aside>

            <section class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="border-b border-zinc-200 pb-6">
                    <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Formulário</p>
                    <h2 class="mt-2 text-3xl font-black leading-tight">Dados para inscrição</h2>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">
                        Os campos abaixo já organizam as informações essenciais para a etapa de inscrição.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mt-6 rounded-md border border-race-cyan/30 bg-amber-50 p-4 text-sm font-bold text-race-ink">
                        {{ session('status') }}
                    </div>
                @endif

                @error('checkout')
                    <div class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-900">
                        {{ $message }}
                    </div>
                @enderror

                @error('registration')
                    <div class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-900">
                        {{ $message }}
                    </div>
                @enderror

                @if ($eventSetting->registrationDeadlineHasPassed() || $eventSetting->registrationLimitHasBeenReached())
                    <div class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-900">
                        {{ $eventSetting->registrationDeadlineHasPassed() ? 'O prazo para inscrições foi encerrado.' : 'O limite total de inscrições foi atingido.' }}
                    </div>
                @endif

                <form id="registration-form" action="{{ route('registration.store') }}" method="POST" class="mt-7 grid grid-cols-1 gap-5" data-registration-form data-pathfinder-check-url="{{ route('registration.pathfinder.check') }}">
                    @csrf

                    <div class="grid gap-3 rounded-md border border-race-cyan/25 bg-[#f7fbff] p-4" data-registration-progress>
                        <div class="flex items-center justify-between gap-3">
                            <p class="text-sm font-black text-race-ink" data-registration-progress-label>Etapa 1 de 6</p>
                            <p class="text-xs font-bold uppercase tracking-normal text-race-blue" data-registration-progress-title>Atleta</p>
                        </div>
                        <div class="h-2 overflow-hidden rounded-full bg-white">
                            <div class="h-full rounded-full bg-race-cyan transition-all" style="width: 16.6667%" data-registration-progress-bar></div>
                        </div>
                    </div>

                    <fieldset class="grid min-w-0 gap-5 border-b border-zinc-200 pb-6" data-registration-step data-step-title="Atleta">
                        <legend class="mb-4 text-base font-black text-zinc-950">Atleta</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome do atleta</span>
                                <input type="text" name="athlete_name" value="{{ old('athlete_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Nome completo" required>
                                @error('athlete_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CPF do atleta</span>
                                <input type="text" name="participant_cpf" value="{{ old('participant_cpf') }}" inputmode="numeric" data-mask="cpf" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="000.000.000-00" required>
                                @error('participant_cpf')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Data de nascimento</span>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" required>
                                @error('birth_date')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Sexo</span>
                                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                                    @foreach (\App\Models\ParticipantRegistration::sexOptions() as $value => $label)
                                        <label class="flex min-h-12 items-center gap-3 rounded-md border border-zinc-200 px-4 py-3 text-sm font-bold text-zinc-800 transition has-checked:border-race-cyan has-checked:bg-amber-50">
                                            <input type="radio" name="sex" value="{{ $value }}" @checked(old('sex') === $value) class="size-4 accent-race-cyan" required>
                                            <span>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('sex')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </div>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Telefone</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel" data-mask="phone" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="(00) 00000-0000" required>
                                @error('phone')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2 md:col-span-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">E-mail</span>
                                <input type="email" name="email" value="{{ old('email') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="email@exemplo.com" required>
                                @error('email')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-zinc-50 p-4 md:col-span-2">
                                <input type="hidden" name="filled_by_legal_representative" value="0">
                                <input type="checkbox" name="filled_by_legal_representative" value="1" @checked(old('filled_by_legal_representative')) class="mt-1 size-4 shrink-0 accent-race-cyan" data-legal-representative-checkbox>
                                <span class="grid gap-1">
                                    <span class="text-sm font-bold leading-5 text-zinc-800">O preenchimento está sendo realizado pelo representante legal</span>
                                    <span class="text-xs font-semibold leading-5 text-zinc-500">Em razão de menoridade, tutela, curatela ou impossibilidade do titular de responder por si.</span>
                                </span>
                            </label>
                            @error('filled_by_legal_representative')
                                <span class="text-sm font-semibold text-red-700 md:col-span-2">{{ $message }}</span>
                            @enderror
                        </div>
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-5 border-b border-zinc-200 pb-6" data-registration-step data-guardian-step data-step-title="Responsavel legal">
                        <legend class="mb-4 text-base font-black text-zinc-950">Responsável Legal</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome do representante legal</span>
                                <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Nome completo">
                                @error('guardian_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CPF do representante legal</span>
                                <input type="text" name="guardian_cpf" value="{{ old('guardian_cpf') }}" inputmode="numeric" data-mask="cpf" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="000.000.000-00">
                                @error('guardian_cpf')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </fieldset>

                    @if (\App\Models\PaymentGatewaySetting::current()->isConfigured() && ! \App\Models\PaymentGatewaySetting::current()->hasManualPix())
                    <fieldset class="grid min-w-0 gap-5 border-b border-zinc-200 pb-6" data-registration-step data-step-title="Pagador">
                        <legend class="mb-4 text-base font-black text-zinc-950">Pagador</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome completo do pagador</span>
                                <input type="text" name="billing_name" value="{{ old('billing_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Nome completo" required>
                                @error('billing_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CPF/CNPJ do pagador</span>
                                <input type="text" name="billing_document" value="{{ old('billing_document') }}" inputmode="numeric" data-mask="cpfCnpj" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="000.000.000-00" required>
                                @error('billing_document')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2 md:col-span-2 lg:col-span-1">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Endereço</span>
                                <input type="text" name="billing_address" value="{{ old('billing_address') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Rua, avenida ou travessa" required>
                                @error('billing_address')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="grid min-w-0 grid-cols-1 gap-5 sm:grid-cols-[minmax(0,1fr)_8rem]">
                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-bold leading-5 text-zinc-800">Bairro</span>
                                    <input type="text" name="billing_province" value="{{ old('billing_province') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Bairro" required>
                                    @error('billing_province')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-bold leading-5 text-zinc-800">Número</span>
                                    <input type="text" name="billing_address_number" value="{{ old('billing_address_number') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="123" required>
                                    @error('billing_address_number')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CEP</span>
                                <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code') }}" inputmode="numeric" autocomplete="postal-code" data-mask="cep" maxlength="9" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="00000-000" required>
                                @error('billing_postal_code')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </fieldset>
                    @endif

                    <fieldset class="grid min-w-0 gap-3 border-b border-zinc-200 pb-6" data-registration-step data-step-title="Prova">
                        <legend class="mb-4 text-base font-black text-zinc-950">Prova</legend>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2" data-modality-options>
                            @forelse ($modalities as $modality)
                                @php($modalityIsFull = $modality->participantLimitHasBeenReached())
                                <label class="flex min-h-20 items-start gap-3 rounded-md border border-zinc-200 px-4 py-3 text-sm transition has-checked:border-race-cyan has-checked:bg-amber-50 has-disabled:cursor-not-allowed has-disabled:bg-zinc-100 has-disabled:text-zinc-500" data-modality-option data-age-start="{{ $modality->age_start }}" data-age-end="{{ $modality->age_end }}" data-race-date="{{ $modality->ageReferenceDate($eventSetting->eventDateForAgeCalculation())->toDateString() }}">
                                    <input type="radio" name="race_modality_id" value="{{ $modality->id }}" @checked((int) old('race_modality_id') === $modality->id) @disabled($modalityIsFull) data-unavailable="{{ $modalityIsFull ? 'true' : 'false' }}" class="mt-1 size-4 accent-race-cyan" required>
                                    <span class="grid gap-1">
                                        <span class="font-bold">{{ $modality->displayName() }}</span>
                                        <span class="text-zinc-600">{{ $modality->ageRangeLabel() }}</span>
                                        @if ($modalityIsFull)
                                            <span class="font-bold text-red-700">Vagas esgotadas</span>
                                        @endif
                                    </span>
                                </label>
                            @empty
                                <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-950 md:col-span-2">
                                    Nenhuma prova ativa no momento.
                                </div>
                            @endforelse
                        </div>
                        <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-950" data-no-compatible-modality hidden>
                            Informe a data de nascimento do atleta para visualizar as provas disponíveis para a idade dele.
                        </div>
                        @error('race_modality_id')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-3 border-b border-zinc-200 pb-6" data-registration-step data-step-title="Pacote">
                        <legend class="mb-4 text-base font-black text-zinc-950">Pacote</legend>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @forelse ($kits as $kit)
                                <label @if ($kit->type === \App\Models\Kit::TypePathfinder) data-pathfinder-package hidden @endif class="rounded-md border border-zinc-200 text-sm transition has-checked:border-race-cyan has-checked:bg-amber-50">
                                    <span class="flex items-start gap-3 px-4 py-3">
                                        <input type="radio" name="kit_id" value="{{ $kit->id }}" @checked((int) old('kit_id') === $kit->id) @if ($kit->requiresRulesAcknowledgement()) data-special-kit data-kit-name="{{ $kit->name }}" @endif data-has-shirt="{{ $kit->has_shirt ? 'true' : 'false' }}" data-kit-type="{{ $kit->type }}" class="mt-1 size-4 accent-race-cyan" required>
                                        <span class="grid gap-1">
                                            <span class="font-bold">{{ $kit->name }}</span>
                                            <span class="font-black text-race-blue">R$ {{ number_format((float) $kit->price, 2, ',', '.') }}</span>
                                            @if (in_array($kit->type, [\App\Models\Kit::TypePcd60, \App\Models\Kit::TypeSocial], true))
                                                <span class="font-semibold text-race-blue">Pacote com desconto especial. O preço exibido já inclui o desconto.</span>
                                            @endif
                                            @if ($kit->description)
                                                <span class="line-clamp-2 text-zinc-600">{{ $kit->description }}</span>
                                            @endif
                                            @if ($kit->requiresRulesAcknowledgement())
                                                <span hidden data-kit-rules-template>{{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($kit->rules ?: 'Regras em definição.') }}</span>
                                            @endif
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-950 md:col-span-2">
                                    Nenhum pacote ativo no momento.
                                </div>
                            @endforelse
                        </div>
                        @error('kit_id')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                        <label class="grid gap-2" data-shirt-size-field hidden>
                            <span class="text-sm font-bold text-zinc-800">Tamanho da camisa</span>
                            <select name="shirt_size" class="rounded-md border border-zinc-300 bg-white px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" required>
                                <option value="">Selecione o tamanho</option>
                                @foreach (\App\Models\ParticipantRegistration::shirtSizeOptions() as $value => $label)
                                    <option value="{{ $value }}" @selected(old('shirt_size') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <span class="text-xs text-zinc-600">Usado exclusivamente para a separação e entrega do pacote.</span>
                            @error('shirt_size')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>
                        @error('accepted_special_kit_rules')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                        @if ($shirts->isNotEmpty())
                            <div class="grid gap-3 rounded-md border border-zinc-200 bg-zinc-50 p-4">
                                <p class="font-black text-zinc-950">Item avulso adicional (opcional)</p>
                                <select name="shirt_id" class="rounded-md border border-zinc-300 px-4 py-3">
                                    <option value="">Não adicionar item</option>
                                    @foreach ($shirts as $shirt)
                                        <option value="{{ $shirt->id }}" @selected((int) old('shirt_id') === $shirt->id)>{{ $shirt->name }} — R$ {{ number_format($shirt->priceForRegistration(), 2, ',', '.') }}</option>
                                    @endforeach
                                </select>
                                <div class="grid gap-3 sm:grid-cols-2">
                                    <select name="extra_shirt_size" class="rounded-md border border-zinc-300 px-4 py-3">
                                        <option value="">Tamanho</option>
                                        @foreach (\App\Models\ParticipantRegistration::shirtSizeOptions() as $size)
                                            <option value="{{ $size }}" @selected(old('extra_shirt_size') === $size)>{{ $size }}</option>
                                        @endforeach
                                    </select>
                                    <input type="number" name="extra_shirt_quantity" value="{{ old('extra_shirt_quantity', 1) }}" min="1" max="10" class="rounded-md border border-zinc-300 px-4 py-3" aria-label="Quantidade de camisetas">
                                </div>
                                <a href="{{ route('store.index') }}" class="text-sm font-bold text-race-blue underline">Ou compre separadamente na loja</a>
                            </div>
                        @endif
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-5" data-registration-step data-step-title="Observacoes">
                        <legend class="mb-4 text-base font-black text-zinc-950">Contato de emergência</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Contato de emergência</span>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Nome da pessoa">
                                @error('emergency_contact_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Telefone de emergência</span>
                                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" inputmode="tel" data-mask="phone" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="(00) 00000-0000">
                                @error('emergency_contact_phone')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                    </fieldset>

                    <fieldset class="grid min-w-0 gap-4 border-t border-zinc-200 pt-6" data-registration-step data-step-title="Conferencia">
                        <legend class="mb-1 text-base font-black text-zinc-950">Declarações obrigatórias</legend>

                        <div class="rounded-md border border-race-cyan/25 bg-[#f7fbff] p-4">
                            <p class="font-black text-race-ink">Confira os dados informados</p>
                            <dl class="mt-4 grid grid-cols-1 gap-3 text-sm sm:grid-cols-2" data-registration-review></dl>
                        </div>

                        <div class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-race-cyan has-checked:bg-amber-50">
                            <input id="accepted_regulation" type="checkbox" name="accepted_regulation" value="1" @checked(old('accepted_regulation')) class="mt-1 size-4 accent-race-cyan" required>
                            <p>
                                <label for="accepted_regulation">Declaro que li e aceito o </label>
                                <button type="button" data-modal-open="registration-regulation-modal" class="font-black text-race-blue underline decoration-race-blue/35 underline-offset-3 transition hover:text-race-ink">
                                    Regulamento
                                </button>.
                            </p>
                        </div>
                        @error('accepted_regulation')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror

                        <div class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-race-cyan has-checked:bg-amber-50">
                            <input id="accepted_privacy_policy" type="checkbox" name="accepted_privacy_policy" value="1" @checked(old('accepted_privacy_policy')) class="mt-1 size-4 accent-race-cyan" required>
                            <p>
                                <label for="accepted_privacy_policy">Li e concordo com a </label>
                                <button type="button" data-modal-open="registration-privacy-policy-modal" class="font-black text-race-blue underline decoration-race-blue/35 underline-offset-3 transition hover:text-race-ink">
                                    Política de Privacidade
                                </button>.
                            </p>
                        </div>
                        @error('accepted_privacy_policy')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror

                        <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-race-cyan has-checked:bg-amber-50">
                            <input type="checkbox" name="accepted_fitness_declaration" value="1" @checked(old('accepted_fitness_declaration')) class="mt-1 size-4 accent-race-cyan" required>
                            <span>Declaro estar em boas condições de saúde e apto(a) para participar da corrida.</span>
                        </label>
                        @error('accepted_fitness_declaration')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror

                        <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-race-cyan has-checked:bg-amber-50">
                            <input type="checkbox" name="accepted_data_confirmation" value="1" @checked(old('accepted_data_confirmation')) class="mt-1 size-4 accent-race-cyan" required>
                            <span>Confirmo que todas as informações preenchidas são verdadeiras e estou ciente de que dados falsos geram punições previstas em lei.</span>
                        </label>
                        @error('accepted_data_confirmation')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror

                    </fieldset>

                    <div class="flex flex-wrap justify-end gap-3 rounded-md bg-zinc-50 p-5" data-registration-actions>
                        <div class="flex flex-wrap gap-3 sm:justify-end">
                            <button type="button" class="rounded-md border border-zinc-300 bg-white px-5 py-3 text-sm font-black text-zinc-800 transition hover:bg-zinc-50" data-registration-prev>
                                Voltar
                            </button>
                            <button type="button" class="rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:bg-race-ink" data-registration-next>
                                Próximo
                            </button>
                            <button type="submit" @disabled($modalities->isEmpty() || $kits->isEmpty() || $eventSetting->registrationDeadlineHasPassed() || $eventSetting->registrationLimitHasBeenReached()) class="rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:bg-race-ink disabled:cursor-not-allowed disabled:bg-zinc-400" data-registration-submit>
                                Enviar inscrição
                            </button>
                        </div>
                    </div>
                </form>
            </section>
        </main>

        <dialog id="special-kit-rules-modal" aria-labelledby="special-kit-rules-title" class="m-auto w-[min(40rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-amber-950/30 backdrop:bg-race-night/80">
            <div class="grid gap-5 p-6 sm:p-8">
                <div>
                    <p class="text-sm font-black uppercase tracking-wide text-race-cyan">Inscrição especial</p>
                    <h2 id="special-kit-rules-title" class="mt-1 text-2xl font-black leading-tight" data-special-kit-rules-title>Regras do pacote especial</h2>
                </div>
                <div class="grid gap-3 text-sm leading-6 text-zinc-700" data-special-kit-rules-content>
                    <p><strong>PCD:</strong> o desconto já está aplicado e não é necessário anexar laudo durante a inscrição. Na retirada do pacote, apresente documento comprobatório.</p>
                    <p><strong>60+:</strong> o desconto já está aplicado. Na retirada do pacote, apresente documento oficial que comprove a idade.</p>
                </div>
                <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-amber-50 px-4 py-3 text-sm font-semibold text-zinc-800">
                    <input type="checkbox" name="accepted_special_kit_rules" value="1" form="registration-form" @checked(old('accepted_special_kit_rules')) class="mt-1 size-4 accent-race-cyan" data-special-kit-acknowledgement>
                    <span>Li e estou ciente das regras deste pacote.</span>
                </label>
                <div class="flex justify-end gap-3">
                    <button type="button" data-modal-close class="rounded-md border border-zinc-300 bg-white px-4 py-2 text-sm font-black text-zinc-800">Voltar</button>
                    <button type="button" data-special-kit-confirm class="rounded-md bg-race-blue px-4 py-2 text-sm font-black text-white disabled:cursor-not-allowed disabled:bg-zinc-400">Confirmar ciência</button>
                </div>
            </div>
        </dialog>

        <dialog id="registration-regulation-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-amber-950/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-race-cyan">Regulamento</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Regras da corrida</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f7fbff] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-race-cyan/15 bg-white p-5 shadow-sm shadow-amber-950/5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->regulation ?: 'Em revisão') }}
                </div>
            </div>
        </dialog>

        <dialog id="registration-privacy-policy-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-amber-950/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-race-cyan">Privacidade</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Política de Privacidade</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f7fbff] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-race-cyan/15 bg-white p-5 shadow-sm shadow-amber-950/5 sm:p-6">
                    <p><strong>Versão {{ \App\Models\ParticipantRegistration::PrivacyPolicyVersion }}.</strong> Esta Política de Privacidade descreve como a organização da Ave Branca Run trata dados pessoais no fluxo de inscrição, pagamento e operação do evento.</p>

                    <h3>1. Dados coletados</h3>
                    <p>Coletamos dados de identificação e contato do atleta, como nome, data de nascimento, CPF, telefone, e-mail, prova escolhida e dados do responsável legal quando o atleta for menor de idade. O app não coleta nem valida laudo de PCD. Quando houver pagamento por Pix, coletamos o comprovante necessário para conferência.</p>
                    <p>Também coletamos o contato de emergência informado para eventual suporte durante o evento.</p>

                    <h3>2. Finalidades e bases de tratamento</h3>
                    <p>Usamos os dados para criar e administrar a inscrição, identificar atletas, confirmar idade e autorização de menores, viabilizar pagamento, emitir comunicações essenciais sobre o evento, prestar suporte, organizar kits, apuração, segurança, atendimento emergencial, prevenção a fraudes, cumprimento de obrigações legais e defesa de direitos.</p>
                    <p>O contato de emergência é usado somente para segurança do atleta e eventual suporte emergencial durante o evento.</p>

                    <h3>3. Pagamentos</h3>
                    <p>O pagamento é realizado por Pix. O comprovante enviado é armazenado em área restrita e utilizado somente para conferência e confirmação da inscrição.</p>

                    <h3>4. Compartilhamento</h3>
                    <p>Podemos compartilhar dados, no limite necessário, com organizadores, prestadores de tecnologia e suporte, processadores de pagamento, equipe de cronometragem e resultados, logística de kit, comunicação operacional, equipes médicas ou emergenciais, seguradoras quando aplicável, autoridades públicas quando exigido e parceiros necessários à execução do evento. Não vendemos dados pessoais.</p>

                    <h3>5. Resultados, imagens e divulgação pública</h3>
                    <p>Resultados, fotos e vídeos do evento podem divulgar dados compatíveis com a natureza pública da prova, como nome do atleta, número de peito, categoria, equipe, tempo, classificação, fotos e vídeos captados durante o evento.</p>

                    <h3>6. Retenção, exclusão e anonimização</h3>
                    <p>Os dados serão mantidos pelo tempo necessário para inscrição, pagamento, suporte, obrigações legais, fiscais, regulatórias e contratuais, prevenção a fraudes, defesa de direitos e histórico do evento. Após esse período, os dados poderão ser excluídos, anonimizados ou mantidos apenas quando houver base legal para retenção.</p>

                    <h3>7. Direitos LGPD</h3>
                    <p>Você pode solicitar confirmação de tratamento, acesso, correção, anonimização, bloqueio ou eliminação quando aplicável, portabilidade quando cabível, informações sobre compartilhamento, revogação de consentimento e revisão de decisões automatizadas caso venham a ser adotadas.</p>
                    <p>Para exercer direitos ou pedir exclusão/anonimização após o prazo necessário, entre em contato pelo e-mail oficial da organização informado nos canais do evento. Para segurança, poderemos pedir dados mínimos para confirmar a identidade do solicitante antes de atender o pedido.</p>

                    <h3>8. Segurança</h3>
                    <p>Adotamos controles para reduzir acesso indevido e exposição desnecessária, incluindo validação dos formulários, proteção CSRF, acesso administrativo restrito e tratamento separado de informações de saúde e emergência.</p>
                </div>
            </div>
        </dialog>
    </body>
</html>
