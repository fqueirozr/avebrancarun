<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="robots" content="noindex, nofollow">
        <title>Área do atleta | Ave Branca Run</title>
        <link rel="icon" href="{{ asset('images/favicon-60-anos.png') }}" type="image/png">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-race-mist text-zinc-950 antialiased">
        <header class="bg-race-night text-white">
            <div class="mx-auto flex max-w-6xl items-center justify-between gap-4 px-5 py-5 sm:px-8">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/ave-branca-run-logo.png') }}" alt="Ave Branca Run" class="h-14 w-auto">
                </a>
                <span class="rounded-md bg-white/10 px-3 py-2 text-sm font-bold">Área do atleta</span>
            </div>
        </header>

        <main class="mx-auto flex max-w-6xl flex-col gap-6 px-5 py-10 sm:px-8 lg:py-14">
            <section class="overflow-hidden rounded-2xl bg-race-night p-6 text-white shadow-xl sm:p-8">
                <p class="text-sm font-black uppercase tracking-wide text-race-cyan">Inscrição {{ $registration->protocol_number }}</p>
                <div class="mt-3 flex flex-col justify-between gap-5 md:flex-row md:items-end">
                    <div>
                        <h1 class="text-3xl font-black sm:text-5xl">{{ $registration->athlete_name }}</h1>
                        <p class="mt-2 font-semibold text-white/70">{{ $registration->modality }}</p>
                    </div>
                    <span class="w-fit rounded-full px-4 py-2 text-sm font-black {{ $registration->payment_status === 'paid' ? 'bg-emerald-400 text-emerald-950' : ($registration->payment_status === 'cancelled' ? 'bg-red-300 text-red-950' : 'bg-amber-300 text-amber-950') }}">
                        Pagamento {{ mb_strtolower($registration->paymentStatusLabel()) }}
                    </span>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <article class="race-panel p-6 sm:p-8">
                    <h2 class="text-2xl font-black text-race-night">Dados da inscrição</h2>
                    <dl class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Data de nascimento</dt><dd class="mt-1 font-bold">{{ $registration->birth_date->format('d/m/Y') }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Sexo</dt><dd class="mt-1 font-bold">{{ $registration->sexLabel() }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">E-mail</dt><dd class="mt-1 break-all font-bold">{{ $registration->email }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Telefone</dt><dd class="mt-1 font-bold">{{ $registration->phone }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Kit</dt><dd class="mt-1 font-bold">{{ $registration->kit?->name ?? 'Não informado' }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Número de peito</dt><dd class="mt-1 font-bold">{{ $registration->bib_number ?: 'A definir' }}</dd></div>
                    </dl>
                </article>

                <article class="race-panel p-6 sm:p-8">
                    <h2 class="text-2xl font-black text-race-night">Detalhes da prova</h2>
                    <dl class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-2">
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Prova</dt><dd class="mt-1 font-bold">{{ $registration->raceModality?->displayName() ?? $registration->modality }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Categoria</dt><dd class="mt-1 font-bold">{{ $registration->result_category ?: 'A definir' }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Data</dt><dd class="mt-1 font-bold">{{ $registration->raceModality?->race_date?->format('d/m/Y') ?? $eventSetting->event_date ?? 'A definir' }}</dd></div>
                        <div><dt class="text-xs font-black uppercase text-zinc-500">Horário</dt><dd class="mt-1 font-bold">{{ $registration->raceModality?->race_time ? mb_substr($registration->raceModality->race_time, 0, 5) : 'A definir' }}</dd></div>
                        <div class="sm:col-span-2"><dt class="text-xs font-black uppercase text-zinc-500">Local</dt><dd class="mt-1 font-bold">{{ $eventSetting->event_location ?: 'A definir' }}</dd></div>
                    </dl>
                </article>
            </section>

            <section class="race-panel p-6 sm:p-8">
                <div class="flex flex-col justify-between gap-3 sm:flex-row sm:items-center">
                    <div><p class="text-sm font-black uppercase text-race-blue">Resultado oficial</p><h2 class="mt-1 text-3xl font-black text-race-night">{{ $registration->resultStatusLabel() }}</h2></div>
                    @if ($registration->result_status === 'finished')
                        <p class="font-mono text-4xl font-black text-race-blue sm:text-5xl">{{ $registration->elapsed_time }}</p>
                    @endif
                </div>

                @if ($registration->result_status === 'finished')
                    <div class="mt-7 grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div class="rounded-xl bg-race-mist p-5"><p class="text-xs font-black uppercase text-zinc-500">Ranking geral</p><p class="mt-2 text-3xl font-black">{{ $registration->overall_rank ? $registration->overall_rank.'º' : '—' }}</p></div>
                        <div class="rounded-xl bg-race-mist p-5"><p class="text-xs font-black uppercase text-zinc-500">Ranking por sexo</p><p class="mt-2 text-3xl font-black">{{ $registration->sex_rank ? $registration->sex_rank.'º' : '—' }}</p></div>
                        <div class="rounded-xl bg-race-mist p-5"><p class="text-xs font-black uppercase text-zinc-500">Ranking da categoria</p><p class="mt-2 text-3xl font-black">{{ $registration->category_rank ? $registration->category_rank.'º' : '—' }}</p></div>
                    </div>
                @else
                    <p class="mt-5 max-w-2xl font-semibold leading-7 text-zinc-600">O resultado aparecerá aqui após a apuração e publicação pela organização.</p>
                @endif
            </section>

            <p class="text-center text-sm font-semibold text-zinc-500">Este link é pessoal. Não compartilhe com terceiros.</p>
        </main>
    </body>
</html>
