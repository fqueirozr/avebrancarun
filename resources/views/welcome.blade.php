<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Corrida de rua do Clube de Desbravadores Ave Branca, com provas para diferentes categorias e percursos de 3 km e 6 km.">

        <title>Ave Branca Run</title>

        <link rel="icon" href="{{ asset('images/favicon-60-anos.png') }}" type="image/png">
        <link rel="apple-touch-icon" href="{{ asset('images/favicon-60-anos.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-race-mist text-zinc-950 antialiased">
        <header class="absolute inset-x-0 top-0 z-30">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-5 sm:px-8" aria-label="Navegação principal">
                <a href="{{ route('home') }}" class="flex min-w-0 items-center gap-3">
                    <img
                        src="{{ asset('images/ave-branca-run-logo.png') }}"
                        alt="Ave Branca Run"
                        class="h-14 w-auto max-w-[190px] object-contain drop-shadow-xl sm:h-16 sm:max-w-[260px]"
                    >
                </a>

                <div class="hidden items-center gap-1 rounded-md border border-white/15 bg-race-night/35 p-1 text-sm font-bold text-white/80 shadow-lg shadow-race-night/20 backdrop-blur-xl md:flex">
                    <a href="#provas" class="rounded-md px-4 py-2 transition hover:bg-white/10 hover:text-white">Provas</a>
                    <a href="#programacao" class="rounded-md px-4 py-2 transition hover:bg-white/10 hover:text-white">Evento</a>
                    <a href="#contato" class="rounded-md px-4 py-2 transition hover:bg-white/10 hover:text-white">Contato</a>
                </div>

                <a href="{{ route('registration') }}" class="shrink-0 rounded-md bg-race-cyan px-4 py-2.5 text-sm font-black text-race-night shadow-lg shadow-race-night/20 transition hover:-translate-y-0.5 hover:bg-race-ice focus:outline-none focus:ring-3 focus:ring-race-cyan/40">
                    Inscrever-se
                </a>
            </nav>
        </header>

        <main class="overflow-hidden">
            <section class="relative min-h-[100svh] overflow-hidden bg-race-night text-white">
                <h1 class="sr-only">Ave Branca Run</h1>

                <div class="absolute inset-0 scale-110 parallax-layer" data-parallax-speed="0.14">
                    <img
                        src="{{ asset('images/ave-branca-run-2026-hero-client.png') }}"
                        alt="Arte da 2ª edição Ave Branca Run com data, horário, local e distâncias da corrida"
                        class="h-full w-full object-cover object-center"
                    >
                </div>

                <div class="absolute inset-0 bg-[linear-gradient(90deg,rgba(2,7,18,0.26)_0%,rgba(2,7,18,0.08)_44%,rgba(2,7,18,0)_100%)]"></div>
                <div class="absolute inset-x-0 top-0 h-32 bg-linear-to-b from-race-night/75 via-race-night/30 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 h-44 bg-linear-to-t from-race-night/78 via-race-night/32 to-transparent"></div>

                <div class="relative z-10 mx-auto flex min-h-[100svh] max-w-7xl items-end justify-center px-5 pb-18 pt-32 sm:px-8 lg:pb-20">
                    <div class="w-full max-w-md reveal-on-scroll lg:max-w-none" data-reveal>
                        <div class="flex flex-col justify-center gap-3 sm:flex-row">
                            <a href="{{ route('registration') }}" class="rounded-md bg-race-cyan px-6 py-3.5 text-center text-base font-black text-race-night shadow-xl shadow-race-night/30 transition hover:-translate-y-0.5 hover:bg-race-ice focus:outline-none focus:ring-3 focus:ring-race-cyan/40">
                                Fazer inscrição
                            </a>
                            <a href="#provas" class="rounded-md border border-white/18 bg-white/10 px-6 py-3.5 text-center text-base font-black text-white shadow-xl shadow-race-night/20 backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/16 focus:outline-none focus:ring-3 focus:ring-white/25">
                                Ver provas
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="relative z-20 -mt-14 px-5 sm:px-8">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-3 md:grid-cols-3">
                    <div class="race-panel p-5 reveal-on-scroll" data-reveal>
                        <p class="text-sm font-black uppercase tracking-normal text-race-blue">Público</p>
                        <p class="mt-2 text-2xl font-black">Para todas as categorias</p>
                        <p class="mt-2 text-sm font-semibold leading-6 text-zinc-600">Categorias por idade e distâncias para diferentes ritmos.</p>
                    </div>
                    <div class="race-panel p-5 reveal-on-scroll" data-reveal>
                        <p class="text-sm font-black uppercase tracking-normal text-race-blue">Percursos</p>
                        <p class="mt-2 text-2xl font-black">{{ $modalities->pluck('distance')->filter()->unique()->values()->implode(' / ') ?: 'Em breve' }}</p>
                        <p class="mt-2 text-sm font-semibold leading-6 text-zinc-600">Provas ativas com mapas e informações de largada.</p>
                    </div>
                    <div class="race-panel p-5 reveal-on-scroll" data-reveal>
                        <p class="text-sm font-black uppercase tracking-normal text-race-blue">Pagamento</p>
                        <p class="mt-2 text-2xl font-black">Pix</p>
                        <p class="mt-2 text-sm font-semibold leading-6 text-zinc-600">Pagamento rápido e seguro para concluir sua inscrição.</p>
                    </div>
                </div>
            </section>

            <section id="provas" class="bg-race-mist">
                <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:py-24">
                    <div class="mb-8 reveal-on-scroll" data-reveal>
                        <h2 class="text-3xl font-black leading-tight text-race-night sm:text-5xl">Provas</h2>
                    </div>

                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @forelse ($modalities as $modality)
                            <article class="race-panel group p-6 transition hover:-translate-y-1 hover:border-race-cyan/50 hover:shadow-xl hover:shadow-race-night/10 reveal-on-scroll" data-reveal>
                                <div class="flex items-start justify-between gap-4">
                                    <span class="rounded-md bg-race-cyan/18 px-3 py-1 text-sm font-black text-race-ink">{{ $modality->type }}</span>
                                    <span class="text-right text-sm font-bold text-zinc-500">{{ $modality->ageRangeLabel() }}</span>
                                </div>
                                <p class="mt-7 text-5xl font-black text-race-night">{{ $modality->distance }}</p>
                                <p class="mt-4 text-base font-black text-zinc-800">{{ $modality->name }}</p>
                                @if ($modality->google_maps_embed_url)
                                    <a href="#percurso-{{ $modality->id }}" class="mt-6 inline-flex rounded-md bg-race-blue px-4 py-2.5 text-sm font-black text-white transition group-hover:bg-race-night">
                                        Ver percurso
                                    </a>
                                @endif
                            </article>
                        @empty
                            <div class="race-panel p-5 text-sm font-bold text-race-ink sm:col-span-2 lg:col-span-3">
                                As provas serão divulgadas em breve.
                            </div>
                        @endforelse
                    </div>

                    @if ($kits->isNotEmpty())
                        <div class="relative mt-16 overflow-hidden rounded-md border border-race-blue/10 bg-white px-5 py-8 text-race-night shadow-xl shadow-race-night/8 sm:px-8 sm:py-10 lg:px-10" data-packages-showcase data-theme="light">
                            <div class="pointer-events-none absolute -right-24 -top-32 size-80 rounded-full bg-race-cyan/12 blur-3xl"></div>
                            <div class="pointer-events-none absolute -bottom-32 left-1/4 size-72 rounded-full bg-race-blue/8 blur-3xl"></div>

                            <div class="relative mb-8 grid gap-6 reveal-on-scroll lg:grid-cols-[1fr_auto] lg:items-end" data-reveal>
                                <div class="max-w-2xl">
                                    <div class="mb-4 flex items-center gap-3">
                                        <span class="grid size-10 place-items-center rounded-full bg-race-cyan text-race-night shadow-lg shadow-race-cyan/20" aria-hidden="true">
                                            <svg viewBox="0 0 24 24" fill="none" class="size-5" stroke="currentColor" stroke-width="2">
                                                <path d="M20 12v8H4v-8M2 7h20v5H2zM12 7v13M12 7H7.5a2.5 2.5 0 1 1 0-5C11 2 12 7 12 7Zm0 0h4.5a2.5 2.5 0 1 0 0-5C13 2 12 7 12 7Z"/>
                                            </svg>
                                        </span>
                                        <p class="text-xs font-black uppercase tracking-[0.22em] text-race-blue">Escolha o seu</p>
                                    </div>
                                    <h3 class="text-3xl font-black leading-tight sm:text-5xl">Pacotes para a sua corrida</h3>
                                    <p class="mt-4 max-w-xl text-sm font-semibold leading-6 text-zinc-600 sm:text-base">
                                        Compare as opções, confira o que combina com você e garanta sua participação.
                                    </p>
                                </div>

                                <div class="flex items-center gap-3">
                                    <span class="rounded-full border border-race-blue/10 bg-race-mist px-4 py-2 text-xs font-black text-race-blue">
                                        {{ $kits->count() }} {{ $kits->count() === 1 ? 'opção disponível' : 'opções disponíveis' }}
                                    </span>
                                    <a href="{{ route('registration') }}" class="hidden items-center gap-2 rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-race-night sm:inline-flex">
                                        Fazer inscrição
                                        <span aria-hidden="true">→</span>
                                    </a>
                                </div>
                            </div>

                            <div class="relative grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-3">
                                @foreach ($kits as $kit)
                                    <article class="group flex min-h-full flex-col overflow-hidden rounded-md border border-race-blue/10 bg-white text-race-night shadow-md shadow-race-night/8 transition duration-300 hover:-translate-y-1.5 hover:border-race-cyan/60 hover:shadow-xl hover:shadow-race-night/10 reveal-on-scroll" data-package-card data-reveal>
                                        <div class="relative overflow-hidden bg-race-mist">
                                            @if ($kit->photo_path)
                                                <button type="button" class="block w-full cursor-zoom-in overflow-hidden" data-image-expand data-image-src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($kit->photo_path) }}" data-image-alt="Foto do {{ $kit->name }}">
                                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($kit->photo_path) }}" alt="Foto do {{ $kit->name }}" class="aspect-[4/3] w-full object-cover transition duration-500 group-hover:scale-105">
                                                </button>
                                            @else
                                                <div class="grid aspect-[4/3] place-items-center bg-linear-to-br from-race-mist via-white to-race-ice">
                                                    <span class="grid size-16 place-items-center rounded-full bg-race-night text-race-cyan shadow-xl" aria-hidden="true">
                                                        <svg viewBox="0 0 24 24" fill="none" class="size-7" stroke="currentColor" stroke-width="1.8">
                                                            <path d="M20 12v8H4v-8M2 7h20v5H2zM12 7v13M12 7H7.5a2.5 2.5 0 1 1 0-5C11 2 12 7 12 7Zm0 0h4.5a2.5 2.5 0 1 0 0-5C13 2 12 7 12 7Z"/>
                                                        </svg>
                                                    </span>
                                                </div>
                                            @endif
                                            <span class="absolute left-4 top-4 rounded-full border border-white/30 bg-race-night/85 px-3 py-1.5 text-[0.68rem] font-black uppercase tracking-wider text-white shadow-lg backdrop-blur">
                                                Pacote do atleta
                                            </span>
                                            @if ($kit->has_shirt)
                                                <span class="absolute bottom-4 right-4 rounded-full bg-race-cyan px-3 py-1.5 text-xs font-black text-race-night shadow-lg">
                                                    Camiseta inclusa
                                                </span>
                                            @endif
                                        </div>

                                        <div class="flex flex-1 flex-col p-5 sm:p-6">
                                            <div class="flex items-start justify-between gap-4">
                                                <p class="text-xl font-black leading-tight text-zinc-950">{{ $kit->name }}</p>
                                                <span class="shrink-0 rounded-full bg-race-mist px-2.5 py-1 text-[0.65rem] font-black uppercase tracking-wide text-race-blue">Disponível</span>
                                            </div>
                                            @if ($kit->description)
                                                <p class="mt-3 line-clamp-3 text-sm font-semibold leading-6 text-zinc-600">{{ $kit->description }}</p>
                                            @else
                                                <p class="mt-3 text-sm font-semibold leading-6 text-zinc-500">Tudo o que você precisa para viver a experiência da prova.</p>
                                            @endif
                                            <div class="mt-auto flex items-end justify-between gap-4 border-t border-zinc-100 pt-5">
                                                <div>
                                                    <p class="text-[0.65rem] font-black uppercase tracking-wider text-zinc-400">Investimento</p>
                                                    <p class="mt-1 text-2xl font-black text-race-blue">R$ {{ number_format((float) $kit->price, 2, ',', '.') }}</p>
                                                </div>
                                                <a href="{{ route('registration') }}" class="grid size-11 shrink-0 place-items-center rounded-full bg-race-night text-xl font-black text-white transition group-hover:bg-race-cyan group-hover:text-race-night" aria-label="Escolher {{ $kit->name }}">
                                                    <span aria-hidden="true">→</span>
                                                </a>
                                            </div>
                                        </div>
                                    </article>
                                @endforeach
                            </div>

                            <a href="{{ route('registration') }}" class="relative mt-6 inline-flex w-full items-center justify-center gap-2 rounded-md bg-race-blue px-5 py-3.5 text-sm font-black text-white transition hover:bg-race-night sm:hidden">
                                Fazer inscrição
                                <span aria-hidden="true">→</span>
                            </a>
                        </div>
                    @endif
                </div>
            </section>

            <section id="programacao" class="relative bg-race-night py-20 text-white sm:py-24">
                <div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-race-cyan/55 to-transparent"></div>

                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <div class="grid gap-9 lg:grid-cols-[0.85fr_1.15fr] lg:items-end">
                        <div class="reveal-on-scroll" data-reveal>
                            <p class="text-sm font-black uppercase tracking-normal text-race-cyan">Evento</p>
                            <h2 class="mt-3 text-3xl font-black leading-tight sm:text-5xl">Informações da prova</h2>
                        </div>
                        <p class="max-w-3xl text-base font-semibold leading-8 text-white/70 reveal-on-scroll" data-reveal>
                            Programação, retirada de pacote, regulamento e percursos reunidos em uma área rápida de consultar antes da largada.
                        </p>
                    </div>

                    <div class="mt-10 grid grid-cols-1 gap-5 lg:grid-cols-[1.05fr_0.95fr] lg:items-start">
                        <div class="grid gap-3 reveal-on-scroll" data-reveal>
                            @foreach ([
                                ['titulo' => 'Informações gerais', 'valor' => $eventSetting->general_information, 'modal' => null, 'tipo' => 'general'],
                                ['titulo' => 'Retirada de pacote', 'valor' => $eventSetting->kit_information ?: 'Confira camiseta, número de peito e demais itens definidos pela organização.', 'modal' => null],
                                ['titulo' => 'Guarda-volumes', 'valor' => $eventSetting->baggage_storage_information ?: 'Serviço e orientações serão confirmados pela organização antes do evento.', 'modal' => null],
                                ['titulo' => 'Cronometragem', 'valor' => $eventSetting->timing_information ?: 'As informações de apuração e resultados serão divulgadas nos canais oficiais do evento.', 'modal' => null],
                                ['titulo' => 'Inscrições especiais', 'valor' => $eventSetting->special_registrations_information ?: 'Entre em contato com a organização para necessidades específicas ou orientações adicionais.', 'modal' => null],
                            ] as $item)
                                <details class="group rounded-md border border-white/10 bg-white/10 shadow-sm shadow-race-night/20 transition open:border-race-cyan/45 open:bg-white/15">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-5 text-base font-black text-white marker:hidden">
                                        <span>{{ $item['titulo'] }}</span>
                                        <span class="grid size-7 shrink-0 place-items-center rounded-full bg-race-cyan text-sm leading-none text-race-night transition group-open:rotate-45">+</span>
                                    </summary>
                                    <div class="border-t border-white/10 px-5 pb-5 pt-4">
                                        @if (($item['tipo'] ?? null) === 'general')
                                            <div class="grid gap-3 sm:grid-cols-2">
                                                <div class="rounded-md border border-race-cyan/25 bg-race-cyan/10 p-4">
                                                    <p class="text-xs font-black uppercase tracking-normal text-race-cyan">Data</p>
                                                    <p class="mt-1 text-base font-black leading-6 text-white">{{ $eventSetting->event_date ?: 'A confirmar' }}</p>
                                                </div>
                                                <div class="rounded-md border border-race-cyan/25 bg-race-cyan/10 p-4">
                                                    <p class="text-xs font-black uppercase tracking-normal text-race-cyan">Local</p>
                                                    <p class="mt-1 text-base font-black leading-6 text-white">{{ $eventSetting->event_location ?: 'A confirmar' }}</p>
                                                </div>
                                            </div>

                                            @if ($item['valor'])
                                                <div class="event-rich-content mt-4 text-race-ice [&_*]:text-race-ice [&_a]:text-race-cyan [&_blockquote]:text-race-ice">
                                                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($item['valor']) }}
                                                </div>
                                            @endif
                                        @else
                                            <div class="event-rich-content text-race-ice [&_*]:text-race-ice [&_a]:text-race-cyan [&_blockquote]:text-race-ice">
                                                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($item['valor']) }}
                                            </div>
                                        @endif
                                        @if ($item['modal'] !== null)
                                            <button type="button" data-modal-open="{{ $item['modal'] }}" class="mt-4 inline-flex rounded-md bg-race-cyan px-4 py-2 text-sm font-black text-race-night transition hover:bg-race-ice">
                                                {{ $item['botao'] }}
                                            </button>
                                        @endif
                                    </div>
                                </details>
                            @endforeach

                            <button type="button" data-modal-open="regulation-modal" class="flex items-center justify-between gap-4 rounded-md border border-white/10 bg-white/10 px-5 py-5 text-left text-base font-black text-white shadow-sm shadow-race-night/20 transition hover:border-race-cyan/45 hover:bg-white/15">
                                <span>Regulamento</span>
                                <span class="text-2xl leading-none text-race-cyan" aria-hidden="true">↗</span>
                            </button>
                        </div>

                        <aside id="percursos" class="race-panel-dark p-4 sm:p-5 reveal-on-scroll" data-reveal>
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-black text-race-cyan">Percursos</p>
                                    <p class="mt-1 text-xs font-bold text-white/60">Provas ativas do evento</p>
                                </div>

                                <p class="w-fit rounded-md bg-white/10 px-3 py-1.5 text-sm font-black text-race-ice">
                                    {{ $modalities->count() > 0 ? $modalities->count().' provas' : 'Em breve' }}
                                </p>
                            </div>

                            @if ($modalities->isNotEmpty())
                                <div class="mt-5 grid gap-4" data-course-tabs>
                                    <div class="flex gap-2 overflow-x-auto rounded-md border border-white/10 bg-white/10 p-2" role="tablist" aria-label="Percursos por prova">
                                        @foreach ($modalities as $modality)
                                            <button
                                                type="button"
                                                id="percurso-tab-{{ $modality->id }}"
                                                class="shrink-0 rounded-md px-4 py-2 text-left text-sm font-black text-white/75 transition hover:bg-white/10 hover:text-white aria-selected:bg-race-cyan aria-selected:text-race-night"
                                                role="tab"
                                                aria-selected="{{ $loop->first ? 'true' : 'false' }}"
                                                aria-controls="percurso-{{ $modality->id }}"
                                                data-course-tab
                                            >
                                                <span class="block">{{ $modality->distance ?: $modality->name }}</span>
                                                <span class="mt-1 block text-xs font-semibold opacity-75">{{ $modality->type }}</span>
                                            </button>
                                        @endforeach
                                    </div>

                                    @foreach ($modalities as $modality)
                                        @php
                                            $modalityCourseImages = $modality->course_images ?? [];
                                            $modalityCourseImage = $modalityCourseImages[0] ?? null;
                                            $modalityCourseImageUrl = $modalityCourseImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($modalityCourseImage) : null;
                                            $modalityRaceDate = $modality->race_date?->format('d/m/Y');
                                            $modalityRaceTime = $modality->race_time ? str($modality->race_time)->substr(0, 5)->toString() : null;
                                        @endphp

                                        <div
                                            id="percurso-{{ $modality->id }}"
                                            class="grid gap-3"
                                            role="tabpanel"
                                            aria-labelledby="percurso-tab-{{ $modality->id }}"
                                            data-course-panel
                                            @if (! $loop->first) hidden @endif
                                        >
                                            <div class="rounded-md border border-white/10 bg-white/10 p-3">
                                                <div class="mb-3 flex flex-col gap-1 sm:flex-row sm:items-end sm:justify-between">
                                                    <div>
                                                        <p class="text-base font-black text-white">{{ $modality->name }}</p>
                                                        <p class="text-sm font-bold text-race-ice">{{ $modality->distance ?: 'Distância a definir' }}</p>
                                                        @if ($modalityRaceDate || $modalityRaceTime)
                                                            <p class="mt-1 text-xs font-bold text-white/65">
                                                                {{ collect([$modalityRaceDate, $modalityRaceTime])->filter()->implode(' às ') }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                    <p class="text-xs font-black uppercase tracking-normal text-white/55">{{ $modality->ageRangeLabel() }}</p>
                                                </div>

                                                @if ($modality->google_maps_embed_url)
                                                    <iframe
                                                        src="{{ $modality->google_maps_embed_url }}"
                                                        title="Mapa do percurso {{ $modality->displayName() }}"
                                                        class="aspect-[4/3] w-full rounded-md border-0 bg-race-ink"
                                                        loading="lazy"
                                                        allowfullscreen
                                                        referrerpolicy="no-referrer-when-downgrade"
                                                    ></iframe>
                                                @elseif ($modalityCourseImageUrl)
                                                    <img src="{{ $modalityCourseImageUrl }}" alt="Imagem do percurso da prova" class="aspect-[4/3] w-full rounded-md object-cover">
                                                @else
                                                    <div class="grid aspect-[4/3] place-items-center rounded-md bg-race-ink px-6 text-center">
                                                        <p class="text-2xl font-black leading-tight text-white">Em breve</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="event-rich-content text-sm leading-6 text-race-ice [&_*]:text-race-ice">
                                                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($modality->course_information ?: $modality->description ?: 'Logo o percurso estará disponível para você se preparar.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-5 rounded-md border border-race-cyan/35 bg-race-cyan/10 p-4 text-sm font-bold text-race-ice">
                                    Logo o percurso estará disponível para você se preparar.
                                </div>
                            @endif
                        </aside>
                    </div>
                </div>
            </section>

            <section id="apoiadores" class="relative overflow-hidden bg-white">
                <div class="absolute inset-x-0 top-0 h-px bg-linear-to-r from-transparent via-race-blue/20 to-transparent"></div>
                <div class="mx-auto max-w-7xl px-5 py-16 sm:px-8 lg:py-20">
                    <div class="text-center reveal-on-scroll" data-reveal>
                        <p class="section-kicker">Quem caminha conosco</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight text-race-night sm:text-5xl">Apoiadores</h2>
                        <p class="mx-auto mt-4 max-w-2xl text-base font-semibold leading-7 text-zinc-600">Instituições que apoiam a missão, a educação e o desenvolvimento de crianças, jovens e famílias.</p>
                    </div>

                    <div class="mt-10 grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">
                        @php
                            $supporters = [
                                ['name' => 'Igreja Adventista do Sétimo Dia', 'url' => 'https://www.adventistas.org/pt/', 'logo' => 'images/supporters/adventistas.png', 'logoClass' => 'max-h-20 max-w-[9rem]'],
                                ['name' => 'Educação Adventista', 'url' => 'https://www.educacaoadventista.org.br/', 'logo' => 'images/supporters/educacao-adventista.png', 'logoClass' => 'max-h-20 max-w-[10rem]'],
                                ['name' => 'Clube de Desbravadores', 'url' => 'https://www.adventistas.org/pt/desbravadores/', 'logo' => 'images/supporters/desbravadores.webp', 'logoClass' => 'max-h-28 max-w-[8rem]'],
                                ['name' => 'T7', 'url' => 'https://t7.org.br/', 'logo' => 'images/supporters/t7.png', 'logoClass' => 'max-h-16 max-w-[11rem]'],
                            ];
                        @endphp

                        @foreach ($supporters as $supporter)
                            <a href="{{ $supporter['url'] }}" target="_blank" rel="noopener noreferrer" aria-label="Visitar o site de {{ $supporter['name'] }}" class="group race-panel flex min-h-44 flex-col items-center justify-center gap-4 p-5 text-center transition hover:-translate-y-1 hover:border-race-cyan/60 hover:shadow-xl hover:shadow-race-night/10 focus:outline-none focus:ring-3 focus:ring-race-cyan/35 reveal-on-scroll" data-reveal>
                                <img src="{{ asset($supporter['logo']) }}" alt="Logo {{ $supporter['name'] }}" class="h-auto w-auto object-contain transition duration-300 group-hover:scale-105 {{ $supporter['logoClass'] }}">
                                <span class="text-sm font-black leading-5 text-race-night">{{ $supporter['name'] }}</span>
                            </a>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="contato" class="bg-[linear-gradient(180deg,#eef7ff_0%,#ffffff_100%)]">
                <div class="mx-auto max-w-7xl px-5 py-20 sm:px-8 lg:py-24">
                    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[0.82fr_1.18fr] lg:items-start">
                        <div class="race-panel-dark p-6 sm:p-7 reveal-on-scroll" data-reveal>
                            <p class="text-sm font-black uppercase tracking-normal text-race-cyan">Contato</p>
                            <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Fale com a organização</h2>
                            <div class="mt-7 grid gap-5 text-sm font-semibold leading-6 text-white/75">
                                @if ($eventSetting->contact_email)
                                    <p>
                                        <span class="block font-black text-white">E-mail</span>
                                        <a href="mailto:{{ $eventSetting->contact_email }}" class="hover:text-race-cyan">{{ $eventSetting->contact_email }}</a>
                                    </p>
                                @endif

                                @if ($eventSetting->contact_phone)
                                    <p>
                                        <span class="block font-black text-white">Telefone</span>
                                        {{ $eventSetting->contact_phone }}
                                    </p>
                                @endif

                                @if ($eventSetting->contact_whatsapp)
                                    <p>
                                        <span class="block font-black text-white">WhatsApp</span>
                                        {{ $eventSetting->contact_whatsapp }}
                                    </p>
                                @endif

                                @unless ($eventSetting->contact_email || $eventSetting->contact_phone || $eventSetting->contact_whatsapp)
                                    <p>Envie sua mensagem pelo formulário. A organização responderá pelo e-mail informado.</p>
                                @endunless
                            </div>
                        </div>

                        <form action="{{ route('contact.store') }}" method="POST" class="race-panel grid gap-5 p-5 sm:p-7 reveal-on-scroll" data-reveal>
                            @csrf

                            <div>
                                <p class="section-kicker">Mensagem</p>
                                <h3 class="mt-2 text-2xl font-black leading-tight sm:text-3xl">Envie sua dúvida</h3>
                            </div>

                            @if (session('contact_status'))
                                <div class="rounded-md border border-race-cyan/30 bg-race-cyan/12 p-4 text-sm font-black text-race-ink">
                                    {{ session('contact_status') }}
                                </div>
                            @endif

                            <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-black leading-5 text-zinc-800">Nome</span>
                                    <input type="text" name="name" value="{{ old('name') }}" class="min-w-0 rounded-md border border-zinc-300 bg-white px-4 py-3 text-base outline-none transition focus:border-race-blue focus:ring-3 focus:ring-race-cyan/25" placeholder="Seu nome" required>
                                    @error('name')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-black leading-5 text-zinc-800">E-mail</span>
                                    <input type="email" name="email" value="{{ old('email') }}" class="min-w-0 rounded-md border border-zinc-300 bg-white px-4 py-3 text-base outline-none transition focus:border-race-blue focus:ring-3 focus:ring-race-cyan/25" placeholder="email@exemplo.com" required>
                                    @error('email')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-black leading-5 text-zinc-800">Telefone</span>
                                    <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel" data-mask="phone" class="min-w-0 rounded-md border border-zinc-300 bg-white px-4 py-3 text-base outline-none transition focus:border-race-blue focus:ring-3 focus:ring-race-cyan/25" placeholder="(00) 00000-0000">
                                    @error('phone')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>

                                <label class="grid min-w-0 gap-2">
                                    <span class="text-sm font-black leading-5 text-zinc-800">Assunto</span>
                                    <input type="text" name="subject" value="{{ old('subject') }}" class="min-w-0 rounded-md border border-zinc-300 bg-white px-4 py-3 text-base outline-none transition focus:border-race-blue focus:ring-3 focus:ring-race-cyan/25" placeholder="Sobre o que quer falar?">
                                    @error('subject')
                                        <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                    @enderror
                                </label>
                            </div>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-black leading-5 text-zinc-800">Mensagem</span>
                                <textarea name="message" rows="5" class="min-w-0 rounded-md border border-zinc-300 bg-white px-4 py-3 text-base outline-none transition focus:border-race-blue focus:ring-3 focus:ring-race-cyan/25" placeholder="Escreva sua dúvida ou solicitação" required>{{ old('message') }}</textarea>
                                @error('message')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <div class="flex justify-end">
                                <button type="submit" class="rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:-translate-y-0.5 hover:bg-race-night focus:outline-none focus:ring-3 focus:ring-race-blue/25">
                                    Enviar mensagem
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-race-blue/10 bg-white">
            <div class="mx-auto grid max-w-7xl gap-3 px-5 py-8 text-center text-sm font-semibold text-zinc-600 sm:px-8 md:items-center">
                <div class="grid gap-1">
                    <p class="font-black text-race-night">Ave Branca Run - 2026</p>
                    @if ($eventSetting->organizer_legal_name || $eventSetting->organizer_cnpj)
                        <p>
                            <span class="font-bold text-zinc-700">Responsável:</span>
                            {{ $eventSetting->organizer_legal_name ?: 'Organização do evento' }}
                            @if ($eventSetting->organizer_cnpj)
                                <span class="whitespace-nowrap">— CNPJ {{ $eventSetting->organizer_cnpj }}</span>
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </footer>

        <dialog id="kit-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-blue/10 bg-white p-0 text-zinc-950 shadow-2xl shadow-race-night/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-black uppercase tracking-normal text-race-cyan">Pacote do atleta</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Informações do pacote</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-race-mist p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal race-panel p-5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->kit_information ?: 'Em definição') }}
                </div>
            </div>
        </dialog>

        <dialog id="package-image-modal" class="m-auto max-h-[92vh] w-[min(64rem,calc(100vw-2rem))] overflow-hidden rounded-md bg-race-night p-0 shadow-2xl backdrop:bg-race-night/85">
            <div class="flex justify-end p-3">
                <button type="button" data-modal-close class="rounded-md bg-white px-4 py-2 text-sm font-black text-race-night">Fechar</button>
            </div>
            <img data-expanded-image src="" alt="" class="max-h-[80vh] w-full object-contain">
        </dialog>

        <dialog id="regulation-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-blue/10 bg-white p-0 text-zinc-950 shadow-2xl shadow-race-night/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-black uppercase tracking-normal text-race-cyan">Regulamento</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Regras da corrida</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-race-mist p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal race-panel p-5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->regulation ?: 'Em revisão') }}
                </div>
            </div>
        </dialog>
    </body>
</html>
