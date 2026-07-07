<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="Corrida de rua do Clube de Desbravadores Ave Branca, com modalidades infantis e percursos adultos de 3 km e 6 km.">

        <title>Corrida Ave Branca</title>

        <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
        <link rel="apple-touch-icon" href="{{ asset('images/ave-branca-logo.png') }}">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="bg-[#f8faf5] text-zinc-950 antialiased">
        <header class="absolute inset-x-0 top-0 z-20">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Navegacao principal">
                <a href="{{ route('home') }}" class="flex items-center gap-3 text-white">
                    <img
                        src="{{ asset('images/ave-branca-logo.png') }}"
                        alt="Logo do Clube de Desbravadores Ave Branca"
                        class="size-14 rounded-full border-2 border-white/80 bg-white object-cover shadow-sm"
                    >
                    <span class="text-sm font-semibold uppercase tracking-normal">Clube Ave Branca</span>
                </a>

                <div class="hidden items-center gap-7 text-sm font-semibold text-white/90 md:flex">
                    <a href="#modalidades" class="hover:text-white">Modalidades</a>
                    <a href="#programacao" class="hover:text-white">Programacao</a>
                    <a href="#informacoes" class="hover:text-white">Informacoes</a>
                </div>

                <a href="{{ route('registration') }}" class="rounded-md bg-white px-4 py-2 text-sm font-bold text-emerald-900 shadow-sm transition hover:bg-lime-100">
                    Inscrever-se
                </a>
            </nav>
        </header>

        <main>
            <section class="relative min-h-[720px] overflow-hidden bg-zinc-950 text-white">
                <img
                    src="{{ asset('images/ave-branca-corrida-hero.png') }}"
                    alt="Corredores participando de uma corrida de rua ao nascer do sol"
                    class="absolute inset-0 h-full w-full object-cover"
                >
                <div class="absolute inset-0 bg-linear-to-r from-emerald-950/95 via-emerald-950/72 to-zinc-950/20"></div>
                <div class="absolute inset-x-0 bottom-0 h-32 bg-linear-to-t from-[#f8faf5] to-transparent"></div>

                <div class="relative z-10 mx-auto flex min-h-[720px] max-w-7xl items-end px-5 pb-24 pt-36 sm:px-8 lg:pb-28">
                    <div class="max-w-3xl">
                        <p class="mb-5 inline-flex rounded-md border border-white/25 bg-white/10 px-3 py-1 text-sm font-semibold text-lime-100 backdrop-blur">
                            Corrida de rua para toda a familia
                        </p>
                        <h1 class="text-5xl font-black leading-tight text-white sm:text-6xl lg:text-7xl">
                            Corrida Ave Branca
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/86 sm:text-xl">
                            Um evento do Clube de Desbravadores Ave Branca com provas infantis por faixa etaria e percursos adultos de 3 km e 6 km.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('registration') }}" class="rounded-md bg-lime-300 px-6 py-3 text-center text-base font-black text-emerald-950 shadow-lg shadow-lime-950/20 transition hover:bg-lime-200">
                                Fazer inscricao
                            </a>
                            <a href="#modalidades" class="rounded-md border border-white/30 bg-white/10 px-6 py-3 text-center text-base font-bold text-white backdrop-blur transition hover:bg-white/18">
                                Ver modalidades
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-5 py-10 sm:px-8 md:grid-cols-3">
                <div class="rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-emerald-800">Publico</p>
                    <p class="mt-2 text-2xl font-black">Infantil e adulto</p>
                </div>
                <div class="rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-emerald-800">Percursos</p>
                    <p class="mt-2 text-2xl font-black">100 m a 6 km</p>
                </div>
                <div class="rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm">
                    <p class="text-sm font-semibold text-emerald-800">Pagamento</p>
                    <p class="mt-2 text-2xl font-black">Em analise</p>
                </div>
            </section>

            <section id="modalidades" class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Modalidades</p>
                    <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Categorias por idade e distancia</h2>
                    <p class="mt-4 text-base leading-7 text-zinc-700">
                        As provas infantis sao organizadas por faixa etaria. A partir dos 14 anos, o atleta ja pode participar da corrida adulta de 3 km; a partir dos 16 anos, da corrida adulta de 6 km.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($modalities as $modality)
                        <article class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <span class="rounded-md bg-emerald-100 px-3 py-1 text-sm font-bold text-emerald-800">{{ $modality->type }}</span>
                                <span class="text-sm font-semibold text-zinc-500">{{ $modality->age_range }}</span>
                            </div>
                            <p class="mt-6 text-5xl font-black text-zinc-950">{{ $modality->distance }}</p>
                            <p class="mt-4 text-sm font-bold text-zinc-700">
                                {{ $modality->price === null ? 'Valor a definir' : 'R$ '.number_format((float) $modality->price, 2, ',', '.') }}
                            </p>
                        </article>
                    @empty
                        <div class="rounded-md border border-amber-200 bg-amber-50 p-5 text-sm font-semibold text-amber-950 sm:col-span-2 lg:col-span-3">
                            As modalidades serao divulgadas em breve.
                        </div>
                    @endforelse
                </div>
            </section>

            <section id="programacao" class="bg-white py-16">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-5 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Evento</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Informacoes para divulgar a corrida</h2>
                        <p class="mt-4 text-base leading-7 text-zinc-700">
                            A pagina ja esta preparada para receber data, local, valores, retirada de kits, regulamento e lote promocional assim que a organizacao fechar os detalhes.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ([
                            ['titulo' => 'Data', 'valor' => $eventSetting->event_date ?: 'A confirmar', 'compacto' => true],
                            ['titulo' => 'Local', 'valor' => $eventSetting->event_location ?: 'A confirmar', 'compacto' => true],
                            ['titulo' => 'Kit atleta', 'valor' => $eventSetting->kit_information ?: 'Em definicao', 'compacto' => false],
                            ['titulo' => 'Regulamento', 'valor' => $eventSetting->regulation ?: 'Em revisao', 'compacto' => false],
                        ] as $item)
                            <div class="rounded-md border border-zinc-200 p-5">
                                <p class="text-sm font-semibold text-zinc-500">{{ $item['titulo'] }}</p>
                                @if ($item['compacto'])
                                    <p class="mt-2 whitespace-pre-line text-xl font-black">{{ $item['valor'] }}</p>
                                @else
                                    <div class="event-rich-content mt-2">
                                        {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($item['valor']) }}
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="informacoes" class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_0.85fr] lg:items-center">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Inscricoes</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Fluxo pronto para evoluir para pagamento online</h2>
                        <p class="mt-4 text-base leading-7 text-zinc-700">
                            Enquanto o meio de pagamento ideal esta em analise, a pagina de inscricao organiza os dados do atleta e destaca que a confirmacao sera finalizada apos a definicao do gateway.
                        </p>
                    </div>

                    <div class="rounded-md bg-emerald-950 p-6 text-white shadow-xl shadow-emerald-950/15">
                        <p class="text-sm font-semibold text-lime-200">Proxima etapa</p>
                        <p class="mt-3 text-2xl font-black">Integrar pagamento</p>
                        <p class="mt-3 text-sm leading-6 text-white/78">
                            O formulario pode ser ligado a Pix, cartao ou plataforma externa quando o cliente escolher o provedor.
                        </p>
                        <a href="{{ route('registration') }}" class="mt-6 inline-flex rounded-md bg-lime-300 px-5 py-3 text-sm font-black text-emerald-950 transition hover:bg-lime-200">
                            Abrir inscricao
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-emerald-900/10 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-zinc-600 sm:px-8 md:flex-row md:items-center md:justify-between">
                <p class="font-semibold text-zinc-800">Clube de Desbravadores Ave Branca</p>
                <p>Corrida de rua com inscricoes, modalidades e pagamento em preparacao.</p>
            </div>
        </footer>
    </body>
</html>
