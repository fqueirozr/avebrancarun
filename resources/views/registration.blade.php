<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Página de inscrição da Ave Branca Run.">

        <title>Inscrição | Ave Branca Run</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('images/ave-branca-logo.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#f4fbff] text-zinc-950 antialiased">
        <header class="border-b border-sky-900/10 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Navegação principal">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img
                        src="{{ asset('images/ave-branca-logo.png') }}"
                        alt="Logo do Clube de Desbravadores Ave Branca"
                        class="size-14 rounded-full border border-emerald-900/10 bg-white object-cover shadow-sm"
                    >
                    <span class="text-sm font-semibold uppercase tracking-normal text-race-ink">Ave Branca Run</span>
                </a>

                <a href="{{ route('home') }}" class="rounded-md border border-zinc-200 px-4 py-2 text-sm font-bold text-zinc-800 transition hover:bg-zinc-50">
                    Voltar ao site
                </a>
            </nav>
        </header>

        <main class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-10 sm:px-8 lg:grid-cols-[0.75fr_1.25fr] lg:py-14">
            <aside class="lg:sticky lg:top-8 lg:self-start">
                <div class="overflow-hidden rounded-md bg-race-night text-white shadow-xl shadow-cyan-950/20">
                    <img
                        src="{{ asset('images/ave-branca-run-2026-hero-new.png') }}"
                        alt="Arte da Ave Branca Run com informações da corrida"
                        class="h-auto w-full bg-race-night object-contain"
                    >
                    <div class="p-6">
                        <p class="text-sm font-semibold text-race-cyan">Inscrição da corrida</p>
                        <h1 class="mt-3 text-3xl font-black leading-tight">Ave Branca Run</h1>
                        <p class="mt-4 text-sm leading-6 text-white/78">
                            Preencha os dados do atleta, escolha a modalidade e siga para o checkout seguro com pagamento por Crédito/PIX.
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-md border border-amber-200 bg-amber-50 p-5 text-amber-950">
                    <p class="font-black">Pagamento por Crédito/PIX</p>
                    <p class="mt-2 text-sm leading-6">
                        O pagamento é processado pela ASAAS Gestão Financeira Instituição de Pagamento S.A.
                    </p>
                </div>
            </aside>

            <section class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="border-b border-zinc-200 pb-6">
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Formulário</p>
                    <h2 class="mt-2 text-3xl font-black leading-tight">Dados para inscrição</h2>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">
                        Os campos abaixo já organizam as informações essenciais para a etapa de inscrição.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-950">
                        {{ session('status') }}
                    </div>
                @endif

                @error('checkout')
                    <div class="mt-6 rounded-md border border-red-200 bg-red-50 p-4 text-sm font-bold text-red-900">
                        {{ $message }}
                    </div>
                @enderror

                <form action="{{ route('registration.store') }}" method="POST" class="mt-7 grid grid-cols-1 gap-5">
                    @csrf

                    <fieldset class="grid min-w-0 gap-5 border-b border-zinc-200 pb-6">
                        <legend class="mb-4 text-base font-black text-zinc-950">Participante</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome do atleta</span>
                                <input type="text" name="athlete_name" value="{{ old('athlete_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Nome completo" required>
                                @error('athlete_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CPF do participante</span>
                                <input type="text" name="participant_cpf" value="{{ old('participant_cpf') }}" inputmode="numeric" data-mask="cpf" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="000.000.000-00" required>
                                @error('participant_cpf')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Data de nascimento</span>
                                <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" required>
                                <span class="text-xs font-semibold leading-5 text-emerald-700">Participantes com mais de {{ \App\Models\ParticipantRegistration::SeniorLegalDiscountMinimumAge }} anos recebem desconto legal de {{ number_format(\App\Models\ParticipantRegistration::SeniorLegalDiscountRate * 100, 0, ',', '.') }}% no checkout.</span>
                                @error('birth_date')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Telefone</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel" data-mask="phone" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="(00) 00000-0000" required>
                                @error('phone')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2 md:col-span-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">E-mail</span>
                                <input type="email" name="email" value="{{ old('email') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="email@exemplo.com" required>
                                @error('email')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-5 border-b border-zinc-200 pb-6">
                        <legend class="mb-4 text-base font-black text-zinc-950">Responsável</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome do responsável</span>
                                <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Obrigatório para menores">
                                @error('guardian_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CPF do responsável</span>
                                <input type="text" name="guardian_cpf" value="{{ old('guardian_cpf') }}" inputmode="numeric" data-mask="cpf" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="000.000.000-00">
                                @error('guardian_cpf')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-5 border-b border-zinc-200 pb-6">
                        <legend class="mb-4 text-base font-black text-zinc-950">Pagador</legend>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome completo do pagador</span>
                                <input type="text" name="billing_name" value="{{ old('billing_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Nome completo">
                                @error('billing_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CPF/CNPJ do pagador</span>
                                <input type="text" name="billing_document" value="{{ old('billing_document') }}" inputmode="numeric" data-mask="cpfCnpj" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="000.000.000-00">
                                @error('billing_document')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2 md:col-span-2 lg:col-span-1">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Endereço</span>
                                <input type="text" name="billing_address" value="{{ old('billing_address') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Rua, avenida ou travessa">
                                @error('billing_address')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="grid min-w-0 grid-cols-1 gap-5 sm:grid-cols-[minmax(0,1fr)_8rem]">
                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-bold leading-5 text-zinc-800">Bairro</span>
                                    <input type="text" name="billing_province" value="{{ old('billing_province') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Bairro">
                                    @error('billing_province')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-bold leading-5 text-zinc-800">Número</span>
                                    <input type="text" name="billing_address_number" value="{{ old('billing_address_number') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="123">
                                    @error('billing_address_number')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">CEP</span>
                                <input type="text" name="billing_postal_code" value="{{ old('billing_postal_code') }}" inputmode="numeric" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Somente números">
                                @error('billing_postal_code')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-3 border-b border-zinc-200 pb-6">
                        <legend class="mb-4 text-base font-black text-zinc-950">Modalidade</legend>
                        <div class="rounded-md border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm font-semibold leading-6 text-emerald-950">
                            O desconto legal de {{ number_format(\App\Models\ParticipantRegistration::SeniorLegalDiscountRate * 100, 0, ',', '.') }}% para participantes com mais de {{ \App\Models\ParticipantRegistration::SeniorLegalDiscountMinimumAge }} anos é calculado automaticamente pela data de nascimento informada.
                        </div>
                        <div class="grid grid-cols-1 gap-3 md:grid-cols-2">
                            @forelse ($modalities as $modality)
                                <label class="flex min-h-20 items-start gap-3 rounded-md border border-zinc-200 px-4 py-3 text-sm transition has-checked:border-emerald-700 has-checked:bg-emerald-50">
                                    <input type="radio" name="race_modality_id" value="{{ $modality->id }}" @checked((int) old('race_modality_id') === $modality->id) class="mt-1 size-4 accent-emerald-800" required>
                                    <span class="grid gap-1">
                                        <span class="font-bold">{{ $modality->displayName() }}</span>
                                        <span class="text-zinc-600">
                                            {{ $modality->price === null ? 'Valor a definir' : 'R$ '.number_format((float) $modality->price, 2, ',', '.') }}
                                        </span>
                                    </span>
                                </label>
                            @empty
                                <div class="rounded-md border border-amber-200 bg-amber-50 p-4 text-sm font-semibold text-amber-950 md:col-span-2">
                                    Nenhuma modalidade ativa no momento.
                                </div>
                            @endforelse
                        </div>
                        @error('race_modality_id')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-5">
                        <legend class="mb-4 text-base font-black text-zinc-950">Observações</legend>

                        <label class="grid min-w-0 gap-2">
                            <span class="text-sm font-bold leading-5 text-zinc-800">Detalhes gerais</span>
                            <textarea name="notes" rows="3" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Equipe, preferências operacionais ou detalhe não sensível">{{ old('notes') }}</textarea>
                            @error('notes')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Contato de emergência</span>
                                <input type="text" name="emergency_contact_name" value="{{ old('emergency_contact_name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Nome da pessoa">
                                @error('emergency_contact_name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Telefone de emergência</span>
                                <input type="tel" name="emergency_contact_phone" value="{{ old('emergency_contact_phone') }}" inputmode="tel" data-mask="phone" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="(00) 00000-0000">
                                @error('emergency_contact_phone')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <label class="grid min-w-0 gap-2">
                            <span class="text-sm font-bold leading-5 text-zinc-800">Saúde e suporte emergencial</span>
                            <textarea name="health_notes" rows="4" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Alergias, restrições médicas, medicamentos ou condição relevante para atendimento durante o evento">{{ old('health_notes') }}</textarea>
                            <span class="text-xs font-semibold leading-5 text-zinc-500">Use este campo apenas para informações necessárias à segurança do participante no evento.</span>
                            @error('health_notes')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>
                    </fieldset>

                    <fieldset class="grid min-w-0 gap-4 border-t border-zinc-200 pt-6">
                        <legend class="mb-1 text-base font-black text-zinc-950">Declarações obrigatórias</legend>

                        <div class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-emerald-700 has-checked:bg-emerald-50">
                            <input id="accepted_regulation" type="checkbox" name="accepted_regulation" value="1" @checked(old('accepted_regulation')) class="mt-1 size-4 accent-emerald-800" required>
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

                        <div class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-emerald-700 has-checked:bg-emerald-50">
                            <input id="accepted_privacy_policy" type="checkbox" name="accepted_privacy_policy" value="1" @checked(old('accepted_privacy_policy')) class="mt-1 size-4 accent-emerald-800" required>
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

                        <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-emerald-700 has-checked:bg-emerald-50">
                            <input type="checkbox" name="accepted_fitness_declaration" value="1" @checked(old('accepted_fitness_declaration')) class="mt-1 size-4 accent-emerald-800" required>
                            <span>Declaro estar apto a participar da prova.</span>
                        </label>
                        @error('accepted_fitness_declaration')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror

                        <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-white px-4 py-3 text-sm font-semibold text-zinc-800 transition has-checked:border-emerald-700 has-checked:bg-emerald-50">
                            <input type="checkbox" name="promotional_opt_in" value="1" @checked(old('promotional_opt_in')) class="mt-1 size-4 accent-emerald-800">
                            <span>Autorizo receber comunicações promocionais sobre futuras corridas e ações do organizador. Posso solicitar o descadastramento a qualquer momento.</span>
                        </label>
                        @error('promotional_opt_in')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                    </fieldset>

                    <div class="grid gap-3 rounded-md bg-zinc-50 p-5 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div>
                            <p class="font-black">Confirmação pendente</p>
                            <p class="mt-1 text-sm leading-6 text-zinc-600">A inscrição será registrada e, se houver valor configurado, você seguirá para o pagamento.</p>
                        </div>

                        <button type="submit" @disabled($modalities->isEmpty()) class="rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:bg-race-ink disabled:cursor-not-allowed disabled:bg-zinc-400">
                            Enviar inscrição
                        </button>
                    </div>
                </form>
            </section>
        </main>

        <dialog id="registration-regulation-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-cyan-950/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-race-cyan">Regulamento</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Regras da corrida</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f4fbff] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-race-cyan/15 bg-white p-5 shadow-sm shadow-cyan-950/5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->regulation ?: 'Em revisão') }}
                </div>
            </div>
        </dialog>

        <dialog id="registration-privacy-policy-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-cyan-950/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-race-cyan">Privacidade</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Política de Privacidade</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f4fbff] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-race-cyan/15 bg-white p-5 shadow-sm shadow-cyan-950/5 sm:p-6">
                    <p><strong>Versão {{ \App\Models\ParticipantRegistration::PrivacyPolicyVersion }}.</strong> Esta Política de Privacidade descreve como a organização da Ave Branca Run trata dados pessoais no fluxo de inscrição, pagamento e operação do evento.</p>

                    <h3>1. Dados coletados</h3>
                    <p>Coletamos dados de identificação e contato do participante, como nome, data de nascimento, CPF, telefone, e-mail, modalidade escolhida e dados do responsável legal quando o participante for menor de idade. Quando houver pagamento, coletamos os dados do pagador necessários ao checkout, como nome, CPF ou CNPJ, endereço, número, bairro e CEP.</p>
                    <p>Campos de observações gerais devem ser usados para informações operacionais não sensíveis. Informações de saúde, alergias, medicamentos, restrições médicas, contato de emergência e dados semelhantes devem ser informados apenas nos campos próprios de saúde e emergência.</p>

                    <h3>2. Finalidades e bases de tratamento</h3>
                    <p>Usamos os dados para criar e administrar a inscrição, identificar participantes, confirmar idade e autorização de menores, viabilizar pagamento, emitir comunicações essenciais sobre o evento, prestar suporte, organizar kits, apuração, segurança, atendimento emergencial, prevenção a fraudes, cumprimento de obrigações legais e defesa de direitos.</p>
                    <p>Dados de saúde e emergência são usados somente para segurança do participante e eventual suporte emergencial durante o evento.</p>

                    <h3>3. Pagamentos</h3>
                    <p>O checkout de Crédito/PIX é processado pela ASAAS Gestão Financeira Instituição de Pagamento S.A. Enviamos ao processador apenas os dados necessários para criar e reconciliar cobranças. Esta aplicação não armazena cartão completo, chave Pix, boleto integral ou outro instrumento financeiro completo.</p>

                    <h3>4. Compartilhamento</h3>
                    <p>Podemos compartilhar dados, no limite necessário, com organizadores, prestadores de tecnologia e suporte, processadores de pagamento, equipe de cronometragem e resultados, logística de kit, comunicação operacional, equipes médicas ou emergenciais, seguradoras quando aplicável, autoridades públicas quando exigido e parceiros necessários à execução do evento. Não vendemos dados pessoais.</p>

                    <h3>5. Resultados, imagens e divulgação pública</h3>
                    <p>Resultados, fotos e vídeos do evento podem divulgar dados compatíveis com a natureza pública da prova, como nome do participante, número de peito, categoria, equipe, tempo, classificação, fotos e vídeos captados durante o evento.</p>

                    <h3>6. Comunicações promocionais</h3>
                    <p>Mensagens essenciais sobre inscrição, pagamento, segurança, suporte e alterações do evento não dependem de opt-in promocional. Comunicações promocionais sobre futuras corridas ou ações do organizador só serão enviadas quando você autorizar no formulário, e o descadastramento pode ser solicitado pelo canal indicado nesta política.</p>

                    <h3>7. Retenção, exclusão e anonimização</h3>
                    <p>Os dados serão mantidos pelo tempo necessário para inscrição, pagamento, suporte, obrigações legais, fiscais, regulatórias e contratuais, prevenção a fraudes, defesa de direitos e histórico do evento. Após esse período, os dados poderão ser excluídos, anonimizados ou mantidos apenas quando houver base legal para retenção.</p>

                    <h3>8. Direitos LGPD</h3>
                    <p>Você pode solicitar confirmação de tratamento, acesso, correção, anonimização, bloqueio ou eliminação quando aplicável, portabilidade quando cabível, informações sobre compartilhamento, revogação de consentimento e revisão de decisões automatizadas caso venham a ser adotadas.</p>
                    <p>Para exercer direitos, pedir exclusão/anonimização após o prazo necessário ou solicitar opt-out promocional, entre em contato pelo e-mail oficial da organização informado nos canais do evento. Para segurança, poderemos pedir dados mínimos para confirmar a identidade do solicitante antes de atender o pedido.</p>

                    <h3>9. Segurança</h3>
                    <p>Adotamos controles para reduzir acesso indevido e exposição desnecessária, incluindo validação dos formulários, proteção CSRF, acesso administrativo restrito e tratamento separado de informações de saúde e emergência.</p>
                </div>
            </div>
        </dialog>
    </body>
</html>
