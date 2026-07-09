<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Corrida de rua do Clube de Desbravadores Ave Branca, com provas infantis e percursos adultos de 3 km e 6 km.">

        <title>Ave Branca Run</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('images/ave-branca-logo.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#f7fbff] text-zinc-950 antialiased">
        <header class="absolute inset-x-0 top-0 z-20">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Navegação principal">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                    <img
                        src="{{ asset('images/ave-branca-logo.png') }}"
                        alt="Logo do Clube de Desbravadores Ave Branca"
                        class="size-14 rounded-full border-2 border-white/80 bg-white object-cover shadow-sm"
                    >
                    <span class="text-sm font-semibold uppercase tracking-normal">Ave Branca Run</span>
                </a>

                <div class="hidden items-center gap-7 text-sm font-semibold text-white/90 md:flex">
                    <a href="#provas" class="hover:text-white">Provas</a>
                    <a href="#programacao" class="hover:text-white">Programação</a>
                    <a href="#contato" class="hover:text-white">Contato</a>
                </div>

                <a href="{{ route('registration') }}" class="rounded-md bg-race-cyan px-4 py-2 text-sm font-bold text-race-night shadow-sm shadow-amber-950/20 transition hover:bg-race-ice">
                    Inscrever-se
                </a>
            </nav>
        </header>

        <main>
            <section class="relative overflow-hidden bg-race-night text-white">
                <h1 class="sr-only">Ave Branca Run</h1>
                <img
                    src="{{ asset('images/ave-branca-run-2026-hero-client.png') }}"
                    alt="Arte da 2ª edição Ave Branca Run com data, horário, local e distâncias da corrida"
                    class="h-[min(100svh,900px)] min-h-[620px] w-full object-cover object-center"
                >
                <div class="absolute inset-x-0 top-0 h-32 bg-linear-to-b from-race-night/85 to-transparent"></div>
                <div class="absolute inset-x-0 bottom-0 h-44 bg-linear-to-t from-race-night via-race-night/68 to-transparent"></div>

                <div class="absolute inset-x-0 bottom-0 z-10">
                    <div class="mx-auto flex max-w-7xl flex-col items-center justify-center gap-3 px-5 pb-8 sm:flex-row sm:px-8 lg:pb-10">
                        <a href="{{ route('registration') }}" class="w-full rounded-md bg-race-cyan px-6 py-3 text-center text-base font-black text-race-night shadow-lg shadow-amber-950/30 transition hover:-translate-y-0.5 hover:bg-race-ice sm:w-auto">
                            Fazer inscrição
                        </a>
                        <a href="#provas" class="w-full rounded-md border border-race-cyan/45 bg-race-night/45 px-6 py-3 text-center text-base font-bold text-race-ice backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/12 sm:w-auto">
                            Ver provas
                        </a>
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-5 py-10 sm:px-8 md:grid-cols-3">
                <div class="rounded-md border border-race-cyan/20 bg-white p-5 shadow-sm shadow-amber-950/10">
                    <p class="text-sm font-semibold text-race-blue">Público</p>
                    <p class="mt-2 text-2xl font-black">Infantil e adulto</p>
                </div>
                <div class="rounded-md border border-race-cyan/20 bg-white p-5 shadow-sm shadow-amber-950/10">
                    <p class="text-sm font-semibold text-race-blue">Percursos</p>
                    <p class="mt-2 text-2xl font-black">{{ $modalities->pluck('distance')->filter()->unique()->values()->implode(' / ') ?: 'Em breve' }}</p>
                </div>
                <div class="rounded-md border border-race-cyan/20 bg-white p-5 shadow-sm shadow-amber-950/10">
                    <p class="text-sm font-semibold text-race-blue">Pagamento</p>
                    <p class="mt-2 text-2xl font-black">Crédito/PIX</p>
                    <p class="mt-2 text-sm leading-6 text-zinc-600">Checkout ASAAS Gestão Financeira Instituição de Pagamento S.A.</p>
                </div>
            </section>

            <section id="provas" class="bg-[#f7fbff]">
                <div class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Provas</p>
                    <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Categorias por idade e distância</h2>
                    <p class="mt-4 text-base leading-7 text-zinc-700">
                        As provas infantis são organizadas por faixa etária. A partir dos 14 anos, o atleta já pode participar da corrida adulta de 3 km; a partir dos 16 anos, da corrida adulta de 6 km.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($modalities as $modality)
                        <article class="rounded-md border border-race-cyan/15 bg-white p-6 shadow-sm shadow-amber-950/5 transition hover:-translate-y-1 hover:border-race-cyan/45 hover:shadow-lg hover:shadow-amber-950/10">
                            <div class="flex items-center justify-between gap-4">
                                <span class="rounded-md bg-amber-100 px-3 py-1 text-sm font-bold text-race-ink">{{ $modality->type }}</span>
                                <span class="text-sm font-semibold text-zinc-500">{{ $modality->ageRangeLabel() }}</span>
                            </div>
                            <p class="mt-6 text-5xl font-black text-zinc-950">{{ $modality->distance }}</p>
                            <p class="mt-4 text-sm font-bold text-zinc-700">{{ $modality->name }}</p>
                            @if ($modality->google_maps_embed_url)
                                <a href="#percurso-{{ $modality->id }}" class="mt-5 inline-flex rounded-md bg-race-blue px-4 py-2 text-sm font-black text-white transition hover:bg-race-ink">
                                    Ver percurso
                                </a>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-md border border-amber-200 bg-amber-50 p-5 text-sm font-semibold text-amber-950 sm:col-span-2 lg:col-span-3">
                            As provas serão divulgadas em breve.
                        </div>
                    @endforelse
                </div>

                @if ($kits->isNotEmpty())
                    <div class="mt-12">
                        <div class="max-w-2xl">
                            <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Kits</p>
                            <h3 class="mt-3 text-2xl font-black leading-tight sm:text-3xl">Valores dos kits</h3>
                        </div>

                        <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                            @foreach ($kits as $kit)
                                <article class="overflow-hidden rounded-md border border-race-cyan/15 bg-white shadow-sm shadow-amber-950/5">
                                    @if ($kit->photo_path)
                                        <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($kit->photo_path) }}" alt="Foto do {{ $kit->name }}" class="aspect-[4/3] w-full object-cover">
                                    @endif
                                    <div class="p-5">
                                        <p class="text-lg font-black text-zinc-950">{{ $kit->name }}</p>
                                        <p class="mt-2 text-2xl font-black text-race-blue">R$ {{ number_format((float) $kit->price, 2, ',', '.') }}</p>
                                        @if ($kit->description)
                                            <p class="mt-3 text-sm leading-6 text-zinc-600">{{ $kit->description }}</p>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </div>
                @endif
                </div>
            </section>

            <section id="programacao" class="bg-white py-16">
                <div class="mx-auto max-w-7xl px-5 sm:px-8">
                    <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Evento</p>
                    <h2 class="mt-3 text-4xl font-black leading-tight sm:text-5xl">Informações da prova</h2>

                    @php
                        $courseImages = $eventSetting->course_images ?? [];
                        $courseImage = $courseImages[0] ?? null;
                        $courseImageUrl = $courseImage ? \Illuminate\Support\Facades\Storage::disk('public')->url($courseImage) : null;
                        $defaultGeneralInformation = "Data: ".($eventSetting->event_date ?: 'A confirmar')."\nLocal: ".($eventSetting->event_location ?: 'A confirmar');
                        $generalInformation = $defaultGeneralInformation.($eventSetting->general_information ? "\n\n".$eventSetting->general_information : '');
                    @endphp

                    <div class="mt-9 grid grid-cols-1 gap-4 lg:grid-cols-[1.35fr_1fr] lg:items-start">
                        <div class="grid gap-3">
                            @foreach ([
                                ['titulo' => 'Informações gerais', 'valor' => $generalInformation, 'modal' => null],
                                ['titulo' => 'Retirada de kit', 'valor' => $eventSetting->kit_information ?: 'Confira camiseta, número de peito e demais itens definidos pela organização.', 'modal' => 'kit-modal', 'botao' => 'Ver kit completo'],
                                ['titulo' => 'Guarda-volumes', 'valor' => $eventSetting->baggage_storage_information ?: 'Serviço e orientações serão confirmados pela organização antes do evento.', 'modal' => null],
                                ['titulo' => 'Pelotões de largada', 'valor' => $eventSetting->start_groups_information ?: 'A organização vai orientar os atletas por categoria, idade e distância no dia da prova.', 'modal' => null],
                                ['titulo' => 'Cronometragem', 'valor' => $eventSetting->timing_information ?: 'As informações de apuração e resultados serão divulgadas nos canais oficiais do evento.', 'modal' => null],
                                ['titulo' => 'Inscrições especiais', 'valor' => $eventSetting->special_registrations_information ?: 'Entre em contato com a organização para necessidades específicas ou orientações adicionais.', 'modal' => null],
                            ] as $item)
                                <details class="group rounded-md border border-race-cyan/15 bg-[#f7fbff] shadow-sm shadow-amber-950/5 transition open:border-race-cyan/45 open:bg-white">
                                    <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-5 py-5 text-base font-black text-zinc-950 marker:hidden">
                                        <span>{{ $item['titulo'] }}</span>
                                        <span class="grid size-6 shrink-0 place-items-center rounded-full bg-race-blue text-sm leading-none text-white transition group-open:rotate-45 group-open:bg-race-cyan group-open:text-race-night">+</span>
                                    </summary>
                                    <div class="border-t border-race-cyan/15 px-5 pb-5 pt-4">
                                        <div class="event-rich-content">
                                            {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($item['valor']) }}
                                        </div>
                                        @if ($item['modal'] !== null)
                                            <button type="button" data-modal-open="{{ $item['modal'] }}" class="mt-4 inline-flex rounded-md bg-race-blue px-4 py-2 text-sm font-black text-white transition hover:bg-race-ink">
                                                {{ $item['botao'] }}
                                            </button>
                                        @endif
                                    </div>
                                </details>
                            @endforeach

                            <button type="button" data-modal-open="regulation-modal" class="flex items-center justify-between gap-4 rounded-md border border-race-cyan/15 bg-[#f7fbff] px-5 py-5 text-left text-base font-black text-zinc-950 shadow-sm shadow-amber-950/5 transition hover:border-race-cyan/45 hover:bg-white">
                                <span>Regulamento</span>
                                <span class="text-2xl leading-none text-race-blue" aria-hidden="true">↗</span>
                            </button>
                        </div>

                        <aside id="percursos" class="rounded-md border border-race-cyan/20 bg-race-night p-4 text-white shadow-xl shadow-amber-950/20 sm:p-5">
                            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm font-bold text-race-cyan">Percursos</p>
                                    <p class="mt-1 text-xs font-semibold text-white/70">Provas ativas do evento</p>
                                </div>

                                <p class="text-sm font-bold text-race-ice">
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
                                                        <p class="text-sm font-semibold text-race-ice">{{ $modality->distance ?: 'Distância a definir' }}</p>
                                                    </div>
                                                    <p class="text-xs font-bold uppercase tracking-normal text-white/55">{{ $modality->ageRangeLabel() }}</p>
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
                                                @elseif ($courseImageUrl)
                                                    <img src="{{ $courseImageUrl }}" alt="Imagem do percurso da prova" class="aspect-[4/3] w-full rounded-md object-cover">
                                                @else
                                                    <div class="grid aspect-[4/3] place-items-center rounded-md bg-race-ink px-6 text-center">
                                                        <p class="text-2xl font-black leading-tight text-white">Em breve</p>
                                                    </div>
                                                @endif
                                            </div>

                                            <div class="event-rich-content text-sm leading-6 text-race-ice [&_*]:text-race-ice">
                                                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($modality->description ?: $eventSetting->course_information ?: 'Logo o percurso estará disponível para você se preparar.') }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="mt-5 rounded-md border border-race-cyan/40 bg-race-cyan/10 p-4 text-sm font-bold text-race-ice">
                                    Logo o percurso estará disponível para você se preparar.
                                </div>
                            @endif

                            <div class="hidden">
                                @if (false)
                                    <details class="group overflow-hidden rounded-md border border-white/10 bg-white/10 open:bg-white/20" @if ($loop->first) open @endif>
                                        <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-3 marker:hidden">
                                            <span>
                                                <span class="block text-sm font-black text-white">{{ $modality->name }}</span>
                                                <span class="mt-1 block text-xs font-semibold text-race-ice">{{ $modality->distance ?: 'Distância a definir' }}</span>
                                            </span>
                                            <span class="grid size-6 shrink-0 place-items-center rounded-full bg-race-cyan text-sm leading-none text-race-night transition group-open:rotate-45">+</span>
                                        </summary>

                                        <div class="grid gap-3 border-t border-white/10 p-3">
                                            @if ($modality->google_maps_embed_url)
                                                <iframe
                                                    src="{{ $modality->google_maps_embed_url }}"
                                                    title="Mapa do percurso {{ $modality->displayName() }}"
                                                    class="aspect-[4/3] w-full rounded-md border-0 bg-race-ink"
                                                    loading="lazy"
                                                    allowfullscreen
                                                    referrerpolicy="no-referrer-when-downgrade"
                                                ></iframe>
                                            @elseif ($courseImageUrl)
                                                <img src="{{ $courseImageUrl }}" alt="Imagem do percurso da prova" class="aspect-[4/3] w-full rounded-md object-cover">
                                            @else
                                                <div class="grid aspect-[4/3] place-items-center rounded-md bg-race-ink px-6 text-center">
                                                    <p class="text-2xl font-black leading-tight text-white">Em breve</p>
                                                </div>
                                            @endif

                                            <div class="event-rich-content text-sm leading-6 text-race-ice [&_*]:text-race-ice">
                                                {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($modality->description ?: $eventSetting->course_information ?: 'Logo o percurso estará disponível para você se preparar.') }}
                                            </div>
                                        </div>
                                    </details>
                                @else
                                    <div class="rounded-md border border-race-cyan/40 bg-race-cyan/10 p-4 text-sm font-bold text-race-ice">
                                        Logo o percurso estará disponível para você se preparar.
                                    </div>
                                @endif
                            </div>
                        </aside>
                    </div>
                </div>
            </section>

            <section id="contato" class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[0.85fr_1.15fr] lg:items-start">
                    <div class="rounded-md bg-race-night p-6 text-white shadow-xl shadow-amber-950/20">
                        <p class="text-sm font-semibold text-race-cyan">Contato</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight">Fale com a organização</h2>
                        <div class="mt-6 grid gap-4 text-sm leading-6 text-white/82">
                            @if ($eventSetting->contact_email)
                                <p>
                                    <span class="block font-bold text-white">E-mail</span>
                                    <a href="mailto:{{ $eventSetting->contact_email }}" class="hover:text-race-cyan">{{ $eventSetting->contact_email }}</a>
                                </p>
                            @endif

                            @if ($eventSetting->contact_phone)
                                <p>
                                    <span class="block font-bold text-white">Telefone</span>
                                    {{ $eventSetting->contact_phone }}
                                </p>
                            @endif

                            @if ($eventSetting->contact_whatsapp)
                                <p>
                                    <span class="block font-bold text-white">WhatsApp</span>
                                    {{ $eventSetting->contact_whatsapp }}
                                </p>
                            @endif

                            @unless ($eventSetting->contact_email || $eventSetting->contact_phone || $eventSetting->contact_whatsapp)
                                <p>Envie sua mensagem pelo formulário. A organização responderá pelo e-mail informado.</p>
                            @endunless
                        </div>
                    </div>

                    <form action="{{ route('contact.store') }}" method="POST" class="grid gap-5 rounded-md border border-race-cyan/15 bg-white p-5 shadow-sm shadow-amber-950/5 sm:p-7">
                        @csrf

                        <div>
                            <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Mensagem</p>
                            <h3 class="mt-2 text-2xl font-black leading-tight">Envie sua dúvida</h3>
                        </div>

                        @if (session('contact_status'))
                            <div class="rounded-md border border-race-cyan/30 bg-amber-50 p-4 text-sm font-bold text-race-ink">
                                {{ session('contact_status') }}
                            </div>
                        @endif

                        <div class="grid min-w-0 grid-cols-1 gap-5 md:grid-cols-2">
                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Nome</span>
                                <input type="text" name="name" value="{{ old('name') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Seu nome" required>
                                @error('name')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">E-mail</span>
                                <input type="email" name="email" value="{{ old('email') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="email@exemplo.com" required>
                                @error('email')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Telefone</span>
                                <input type="tel" name="phone" value="{{ old('phone') }}" inputmode="tel" data-mask="phone" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="(00) 00000-0000">
                                @error('phone')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>

                            <label class="grid min-w-0 gap-2">
                                <span class="text-sm font-bold leading-5 text-zinc-800">Assunto</span>
                                <input type="text" name="subject" value="{{ old('subject') }}" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Sobre o que quer falar?">
                                @error('subject')
                                    <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                                @enderror
                            </label>
                        </div>

                        <label class="grid min-w-0 gap-2">
                            <span class="text-sm font-bold leading-5 text-zinc-800">Mensagem</span>
                            <textarea name="message" rows="5" class="min-w-0 rounded-md border border-zinc-300 px-4 py-3 text-base outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Escreva sua dúvida ou solicitação" required>{{ old('message') }}</textarea>
                            @error('message')
                                <span class="text-sm font-semibold text-red-700">{{ $message }}</span>
                            @enderror
                        </label>

                        <div class="flex justify-end">
                            <button type="submit" class="rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:bg-race-ink">
                                Enviar mensagem
                            </button>
                        </div>
                    </form>
                </div>
            </section>
        </main>

        <footer class="border-t border-race-cyan/15 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-zinc-600 sm:px-8 md:flex-row md:items-center md:justify-between">
                <p class="font-semibold text-zinc-800">Ave Branca Run</p>
                <p>Corrida de rua com inscrições, provas e pagamento por Crédito/PIX via ASAAS Gestão Financeira Instituição de Pagamento S.A.</p>
            </div>
        </footer>

        <dialog id="kit-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-amber-950/30 backdrop:bg-race-night/80">
            <div class="flex items-start justify-between gap-5 border-b border-race-cyan/20 bg-race-night p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-race-cyan">Kit atleta</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Informações do kit</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f7fbff] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-race-cyan/15 bg-white p-5 shadow-sm shadow-amber-950/5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->kit_information ?: 'Em definição') }}
                </div>
            </div>
        </dialog>

        <dialog id="regulation-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-race-cyan/15 bg-white p-0 text-zinc-950 shadow-2xl shadow-amber-950/30 backdrop:bg-race-night/80">
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
    </body>
</html>
