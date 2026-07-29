<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Encontre respostas sobre inscrições, retirada do kit, regulamento e participação na Ave Branca Run.">

        <title>FAQ | Ave Branca Run</title>

        <link rel="icon" href="{{ asset('images/favicon-60-anos.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('images/favicon-60-anos.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-race-mist text-zinc-950 antialiased">
        <header class="absolute inset-x-0 top-0 z-30">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-5 sm:px-8" aria-label="Navegação principal">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                    <img
                        src="{{ asset('images/ave-branca-run-logo.png') }}"
                        alt="Ave Branca Run"
                        class="h-14 w-auto max-w-[190px] object-contain drop-shadow-xl sm:h-16 sm:max-w-[260px]"
                    >
                </a>

                <div class="flex items-center gap-2">
                    <a href="{{ route('home') }}" class="hidden rounded-md px-4 py-2.5 text-sm font-bold text-white/80 transition hover:bg-white/10 hover:text-white sm:inline-flex">
                        Início
                    </a>
                    <a href="{{ route('registration') }}" class="shrink-0 rounded-md bg-race-cyan px-4 py-2.5 text-sm font-black text-race-night shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-race-ice focus:outline-none focus:ring-3 focus:ring-race-cyan/40">
                        Inscrever-se
                    </a>
                </div>
            </nav>
        </header>

        <main>
            <section class="relative overflow-hidden bg-race-night px-5 pb-24 pt-36 text-white sm:px-8 sm:pb-30 sm:pt-44">
                <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                    <div class="absolute -right-28 -top-28 size-96 rounded-full bg-race-blue/35 blur-3xl"></div>
                    <div class="absolute -bottom-40 left-1/4 size-96 rounded-full bg-race-cyan/16 blur-3xl"></div>
                    <div class="absolute inset-0 bg-[linear-gradient(115deg,transparent_0%,rgba(255,255,255,0.025)_48%,transparent_49%)] bg-[length:32px_32px]"></div>
                </div>

                <div class="relative mx-auto max-w-7xl">
                    <div class="max-w-3xl">
                        <div class="mb-6 inline-flex items-center gap-2 rounded-full border border-white/12 bg-white/6 px-3 py-1.5 text-xs font-black uppercase tracking-[0.18em] text-race-cyan backdrop-blur">
                            <span class="size-1.5 rounded-full bg-race-cyan shadow-[0_0_12px_rgba(245,181,27,0.9)]"></span>
                            Central de ajuda
                        </div>
                        <h1 class="max-w-2xl text-4xl font-black leading-[1.05] tracking-tight sm:text-6xl lg:text-7xl">
                            Tudo para você chegar
                            <span class="text-race-cyan">pronto à largada.</span>
                        </h1>
                        <p class="mt-6 max-w-2xl text-base font-semibold leading-7 text-white/65 sm:text-lg">
                            Reunimos as informações essenciais sobre inscrições, kit, regulamento e participação em um só lugar.
                        </p>
                    </div>
                </div>
            </section>

            <div class="relative z-10 mx-auto -mt-12 max-w-7xl px-5 pb-20 sm:px-8 sm:pb-24">
                <aside class="grid overflow-hidden rounded-xl border border-white/70 bg-white shadow-2xl shadow-race-night/12 sm:grid-cols-3" aria-label="Resumo da central de ajuda">
                    <div class="flex items-center gap-4 border-b border-race-blue/8 p-5 sm:border-b-0 sm:border-r sm:p-6">
                        <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-race-mist text-race-blue">
                            <svg viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M7 3h10v4H7zM5 5H3v16h18V5h-2M8 12h8M8 16h5"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.15em] text-zinc-400">Informações</p>
                            <p class="mt-1 font-black text-race-night">Direto da organização</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 border-b border-race-blue/8 p-5 sm:border-b-0 sm:border-r sm:p-6">
                        <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-race-mist text-race-blue">
                            <svg viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.15em] text-zinc-400">Conteúdo</p>
                            <p class="mt-1 font-black text-race-night">Atualizado para a prova</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4 p-5 sm:p-6">
                        <span class="grid size-11 shrink-0 place-items-center rounded-lg bg-race-mist text-race-blue">
                            <svg viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="2" aria-hidden="true">
                                <path d="M4 5h16v11H8l-4 4z"/><path d="M8 9h8M8 12h5"/>
                            </svg>
                        </span>
                        <div>
                            <p class="text-xs font-black uppercase tracking-[0.15em] text-zinc-400">Suporte</p>
                            <p class="mt-1 font-black text-race-night">Fale com nossa equipe</p>
                        </div>
                    </div>
                </aside>

                <div class="mt-16 grid gap-14 lg:grid-cols-[0.72fr_1.28fr] lg:gap-18">
                    <div class="lg:sticky lg:top-8 lg:self-start">
                        <p class="section-kicker">Antes da prova</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight tracking-tight text-race-night sm:text-4xl">Informações importantes das inscrições</h2>
                        <p class="mt-4 max-w-md text-sm font-semibold leading-6 text-zinc-600">
                            Confira os pontos essenciais para garantir uma experiência tranquila do cadastro à retirada do pacote.
                        </p>
                        <div class="mt-8 hidden h-px bg-linear-to-r from-race-cyan via-race-blue/30 to-transparent lg:block"></div>
                    </div>

                    <section class="grid gap-3" aria-label="Informações importantes" data-faq-list>
                        @php
                            $importantInformation = [
                                ['title' => 'Informações gerais', 'content' => $eventSetting->general_information],
                                ['title' => 'Retirada do pacote', 'content' => $eventSetting->kit_information],
                                ['title' => 'Inscrições especiais', 'content' => $eventSetting->special_registrations_information],
                                ['title' => 'Regulamento', 'content' => $eventSetting->regulation],
                            ];
                        @endphp

                        @foreach ($importantInformation as $information)
                            @if (filled($information['content']))
                                <details class="group overflow-hidden rounded-xl border border-race-blue/10 bg-white shadow-sm shadow-race-night/5 transition duration-300 open:border-race-cyan/45 open:shadow-xl open:shadow-race-night/8" @if ($loop->first) open @endif>
                                    <summary class="cursor-pointer list-none p-5 marker:hidden sm:p-6">
                                        <span class="flex items-center gap-4">
                                            <span class="grid size-10 shrink-0 place-items-center rounded-lg bg-race-mist text-sm font-black text-race-blue transition group-open:bg-race-cyan group-open:text-race-night">
                                                {{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <span class="flex-1 text-base font-black text-race-night sm:text-lg">{{ $information['title'] }}</span>
                                            <span class="grid size-9 shrink-0 place-items-center rounded-full border border-race-blue/10 bg-race-mist text-race-blue transition duration-300 group-open:rotate-45 group-open:border-race-cyan group-open:bg-race-cyan group-open:text-race-night" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" class="size-4" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                            </span>
                                        </span>
                                    </summary>
                                    <div class="px-5 pb-5 sm:px-6 sm:pb-6">
                                        <div class="event-rich-content ml-0 border-t border-race-blue/10 pt-5 sm:ml-14">
                                            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($information['content']) }}
                                        </div>
                                    </div>
                                </details>
                            @endif
                        @endforeach
                    </section>
                </div>

                @if (filled($eventSetting->faq_items))
                    <section class="mt-20 border-t border-race-blue/10 pt-16 sm:mt-24 sm:pt-20" aria-labelledby="other-questions-title">
                        <div class="mx-auto max-w-2xl text-center">
                            <p class="section-kicker">FAQ</p>
                            <h2 id="other-questions-title" class="mt-3 text-3xl font-black leading-tight tracking-tight text-race-night sm:text-5xl">Outras dúvidas frequentes</h2>
                            <p class="mt-4 text-sm font-semibold leading-6 text-zinc-600 sm:text-base">Respostas rápidas para as perguntas que mais recebemos.</p>
                        </div>

                        <div class="mx-auto mt-10 grid max-w-4xl gap-3" data-faq-list>
                            @foreach ($eventSetting->faq_items as $item)
                                <details class="group overflow-hidden rounded-xl border border-race-blue/10 bg-white shadow-sm shadow-race-night/5 transition duration-300 open:border-race-cyan/45 open:shadow-xl open:shadow-race-night/8">
                                    <summary class="cursor-pointer list-none p-5 marker:hidden sm:p-6">
                                        <span class="flex items-center gap-4">
                                            <span class="grid size-8 shrink-0 place-items-center rounded-full bg-race-mist text-race-blue transition group-open:bg-race-cyan group-open:text-race-night" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" class="size-4" stroke="currentColor" stroke-width="2"><path d="M9.5 9a2.7 2.7 0 1 1 4.4 2.1c-1.2.9-1.9 1.4-1.9 2.9M12 18h.01"/></svg>
                                            </span>
                                            <span class="flex-1 text-base font-black leading-6 text-race-night sm:text-lg">{{ $item['question'] }}</span>
                                            <span class="grid size-9 shrink-0 place-items-center rounded-full border border-race-blue/10 bg-race-mist text-race-blue transition duration-300 group-open:rotate-45 group-open:border-race-cyan group-open:bg-race-cyan group-open:text-race-night" aria-hidden="true">
                                                <svg viewBox="0 0 24 24" fill="none" class="size-4" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                                            </span>
                                        </span>
                                    </summary>
                                    <div class="px-5 pb-5 sm:px-6 sm:pb-6">
                                        <div class="event-rich-content border-t border-race-blue/10 pt-5 sm:ml-12">
                                            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($item['answer']) }}
                                        </div>
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    </section>
                @endif

                <section class="relative mt-20 overflow-hidden rounded-2xl bg-race-night p-7 text-white shadow-2xl shadow-race-night/20 sm:p-10 lg:flex lg:items-center lg:justify-between lg:gap-10">
                    <div class="pointer-events-none absolute -right-20 -top-28 size-72 rounded-full bg-race-blue/45 blur-3xl" aria-hidden="true"></div>
                    <div class="relative max-w-2xl">
                        <p class="text-xs font-black uppercase tracking-[0.2em] text-race-cyan">Ainda precisa de ajuda?</p>
                        <h2 class="mt-3 text-2xl font-black leading-tight sm:text-4xl">Nossa equipe está pronta para ajudar.</h2>
                        <p class="mt-3 text-sm font-semibold leading-6 text-white/65 sm:text-base">Envie sua dúvida diretamente para a organização da Ave Branca Run.</p>
                    </div>
                    <a href="{{ route('home') }}#contato" class="relative mt-7 inline-flex shrink-0 items-center justify-center gap-3 rounded-md bg-race-cyan px-6 py-3.5 text-sm font-black text-race-night shadow-lg shadow-black/20 transition hover:-translate-y-0.5 hover:bg-race-ice focus:outline-none focus:ring-3 focus:ring-race-cyan/40 lg:mt-0">
                        Falar com a organização
                        <span aria-hidden="true">→</span>
                    </a>
                </section>
            </div>
        </main>

        <footer class="border-t border-race-blue/10 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col items-center justify-between gap-3 px-5 py-8 text-center text-sm font-semibold text-zinc-500 sm:px-8 md:flex-row md:text-left">
                <p class="font-black text-race-night">Ave Branca Run — 2026</p>
                <a href="{{ route('home') }}" class="font-bold transition hover:text-race-blue">Voltar para o início</a>
            </div>
        </footer>
    </body>
</html>
