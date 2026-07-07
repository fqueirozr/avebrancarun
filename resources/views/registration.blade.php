<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Pagina de inscricao da Corrida Ave Branca.">

        <title>Inscricao | Corrida Ave Branca</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('images/ave-branca-logo.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#f8faf5] text-zinc-950 antialiased">
        <header class="border-b border-emerald-900/10 bg-white">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Navegacao principal">
                <a href="{{ route('home') }}" class="flex items-center gap-3">
                    <img
                        src="{{ asset('images/ave-branca-logo.png') }}"
                        alt="Logo do Clube de Desbravadores Ave Branca"
                        class="size-14 rounded-full border border-emerald-900/10 bg-white object-cover shadow-sm"
                    >
                    <span class="text-sm font-semibold uppercase tracking-normal text-emerald-950">Clube Ave Branca</span>
                </a>

                <a href="{{ route('home') }}" class="rounded-md border border-zinc-200 px-4 py-2 text-sm font-bold text-zinc-800 transition hover:bg-zinc-50">
                    Voltar ao site
                </a>
            </nav>
        </header>

        <main class="mx-auto grid max-w-7xl grid-cols-1 gap-8 px-5 py-10 sm:px-8 lg:grid-cols-[0.75fr_1.25fr] lg:py-14">
            <aside class="lg:sticky lg:top-8 lg:self-start">
                <div class="overflow-hidden rounded-md bg-emerald-950 text-white shadow-xl shadow-emerald-950/15">
                    <img
                        src="{{ asset('images/ave-branca-corrida-hero.png') }}"
                        alt="Participantes em uma corrida de rua"
                        class="h-56 w-full object-cover"
                    >
                    <div class="p-6">
                        <p class="text-sm font-semibold text-lime-200">Inscricao da corrida</p>
                        <h1 class="mt-3 text-3xl font-black leading-tight">Corrida Ave Branca</h1>
                        <p class="mt-4 text-sm leading-6 text-white/78">
                            Preencha os dados do atleta e escolha a modalidade. A confirmacao de pagamento sera liberada apos a definicao do meio de pagamento.
                        </p>
                    </div>
                </div>

                <div class="mt-5 rounded-md border border-amber-200 bg-amber-50 p-5 text-amber-950">
                    <p class="font-black">Pagamento em analise</p>
                    <p class="mt-2 text-sm leading-6">
                        Esta etapa esta preparada para integracao com Pix, cartao ou plataforma externa assim que o provedor for escolhido.
                    </p>
                </div>
            </aside>

            <section class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm sm:p-7">
                <div class="border-b border-zinc-200 pb-6">
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Formulario</p>
                    <h2 class="mt-2 text-3xl font-black leading-tight">Dados para inscricao</h2>
                    <p class="mt-3 text-sm leading-6 text-zinc-600">
                        Os campos abaixo ja organizam as informacoes essenciais para a etapa de inscricao.
                    </p>
                </div>

                @if (session('status'))
                    <div class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-950">
                        {{ session('status') }}
                    </div>
                @endif

                <form action="{{ route('registration.store') }}" method="POST" class="mt-7 grid grid-cols-1 gap-5">
                    @csrf

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <label class="grid gap-2">
                            <span class="text-sm font-bold text-zinc-800">Nome do atleta</span>
                            <input type="text" name="athlete_name" value="{{ old('athlete_name') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Nome completo" required>
                            @error('athlete_name')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-2">
                            <span class="text-sm font-bold text-zinc-800">Data de nascimento</span>
                            <input type="date" name="birth_date" value="{{ old('birth_date') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" required>
                            @error('birth_date')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2">
                        <label class="grid gap-2">
                            <span class="text-sm font-bold text-zinc-800">Responsavel</span>
                            <input type="text" name="guardian_name" value="{{ old('guardian_name') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Obrigatorio para menores">
                            @error('guardian_name')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>

                        <label class="grid gap-2">
                            <span class="text-sm font-bold text-zinc-800">Telefone</span>
                            <input type="tel" name="phone" value="{{ old('phone') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="(00) 00000-0000" required>
                            @error('phone')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>
                    </div>

                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">E-mail</span>
                        <input type="email" name="email" value="{{ old('email') }}" class="rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="email@exemplo.com" required>
                        @error('email')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                    </label>

                    <fieldset class="grid gap-3">
                        <legend class="text-sm font-bold text-zinc-800">Modalidade</legend>
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

                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">Observacoes</span>
                        <textarea name="notes" rows="4" class="rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-emerald-700 focus:ring-3 focus:ring-emerald-100" placeholder="Equipe, restricao medica ou detalhe importante">{{ old('notes') }}</textarea>
                        @error('notes')
                            <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                        @enderror
                    </label>

                    <div class="grid gap-3 rounded-md bg-zinc-50 p-5 sm:grid-cols-[1fr_auto] sm:items-center">
                        <div>
                            <p class="font-black">Confirmacao pendente</p>
                            <p class="mt-1 text-sm leading-6 text-zinc-600">O envio definitivo e o pagamento serao ativados apos a escolha do gateway.</p>
                        </div>

                        <button type="submit" @disabled($modalities->isEmpty()) class="rounded-md bg-emerald-800 px-5 py-3 text-sm font-black text-white transition hover:bg-emerald-900 disabled:cursor-not-allowed disabled:bg-zinc-400">
                            Enviar inscricao
                        </button>
                    </div>
                </form>
            </section>
        </main>
    </body>
</html>
