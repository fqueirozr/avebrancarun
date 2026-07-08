<!DOCTYPE html>
<html lang="pt-BR">
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
    <body class="bg-[#f7faf2] text-zinc-950 antialiased">
        <header class="absolute inset-x-0 top-0 z-20">
            <nav class="mx-auto flex max-w-7xl items-center justify-between px-5 py-5 sm:px-8" aria-label="Navegação principal">
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
                    <a href="#programacao" class="hover:text-white">Programação</a>
                    <a href="#informacoes" class="hover:text-white">Informações</a>
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
                <div class="absolute inset-0 bg-linear-to-r from-emerald-950/96 via-emerald-950/70 to-zinc-950/18"></div>
                <div class="absolute inset-x-0 bottom-0 h-36 bg-linear-to-t from-[#f7faf2] to-transparent"></div>

                <div class="relative z-10 mx-auto flex min-h-[720px] max-w-7xl items-end px-5 pb-24 pt-36 sm:px-8 lg:pb-28">
                    <div class="max-w-3xl">
                        <p class="mb-5 inline-flex rounded-md border border-white/25 bg-white/12 px-3 py-1 text-sm font-semibold text-lime-100 shadow-sm backdrop-blur">
                            Corrida de rua para toda a família
                        </p>
                        <h1 class="text-5xl font-black leading-tight text-white sm:text-6xl lg:text-7xl">
                            Corrida Ave Branca
                        </h1>
                        <p class="mt-6 max-w-2xl text-lg leading-8 text-white/86 sm:text-xl">
                            Um evento do Clube de Desbravadores Ave Branca com provas infantis por faixa etária e percursos adultos de 3 km e 6 km.
                        </p>
                        <div class="mt-8 flex flex-col gap-3 sm:flex-row">
                            <a href="{{ route('registration') }}" class="rounded-md bg-lime-300 px-6 py-3 text-center text-base font-black text-emerald-950 shadow-lg shadow-lime-950/20 transition hover:-translate-y-0.5 hover:bg-lime-200">
                                Fazer inscrição
                            </a>
                            <a href="#modalidades" class="rounded-md border border-white/30 bg-white/10 px-6 py-3 text-center text-base font-bold text-white backdrop-blur transition hover:-translate-y-0.5 hover:bg-white/18">
                                Ver modalidades
                            </a>
                        </div>
                    </div>
                </div>
            </section>

            <section class="mx-auto grid max-w-7xl grid-cols-1 gap-4 px-5 py-10 sm:px-8 md:grid-cols-3">
                <div class="rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm shadow-emerald-950/5">
                    <p class="text-sm font-semibold text-emerald-800">Público</p>
                    <p class="mt-2 text-2xl font-black">Infantil e adulto</p>
                </div>
                <div class="rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm shadow-emerald-950/5">
                    <p class="text-sm font-semibold text-emerald-800">Percursos</p>
                    <p class="mt-2 text-2xl font-black">100 m a 6 km</p>
                </div>
                <div class="rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm shadow-emerald-950/5">
                    <p class="text-sm font-semibold text-emerald-800">Pagamento</p>
                    <p class="mt-2 text-2xl font-black">Em análise</p>
                </div>
            </section>

            <section id="modalidades" class="mx-auto max-w-7xl px-5 py-14 sm:px-8">
                <div class="max-w-2xl">
                    <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Modalidades</p>
                    <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Categorias por idade e distância</h2>
                    <p class="mt-4 text-base leading-7 text-zinc-700">
                        As provas infantis são organizadas por faixa etária. A partir dos 14 anos, o atleta já pode participar da corrida adulta de 3 km; a partir dos 16 anos, da corrida adulta de 6 km.
                    </p>
                </div>

                <div class="mt-8 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    @forelse ($modalities as $modality)
                        <article class="rounded-md border border-zinc-200 bg-white p-6 shadow-sm shadow-emerald-950/5 transition hover:-translate-y-1 hover:border-emerald-200 hover:shadow-lg hover:shadow-emerald-950/10">
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
                            As modalidades serão divulgadas em breve.
                        </div>
                    @endforelse
                </div>
            </section>

            <section id="programacao" class="bg-white py-16">
                <div class="mx-auto grid max-w-7xl grid-cols-1 gap-10 px-5 sm:px-8 lg:grid-cols-[0.9fr_1.1fr] lg:items-start">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Evento</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Informações para divulgar a corrida</h2>
                        <p class="mt-4 text-base leading-7 text-zinc-700">
                            A página já está preparada para receber data, local, valores, retirada de kits, regulamento e lote promocional assim que a organização fechar os detalhes.
                        </p>
                    </div>

                    <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                        @foreach ([
                            ['titulo' => 'Data', 'valor' => $eventSetting->event_date ?: 'A confirmar', 'modal' => null],
                            ['titulo' => 'Local', 'valor' => $eventSetting->event_location ?: 'A confirmar', 'modal' => null],
                            ['titulo' => 'Kit atleta', 'valor' => $eventSetting->kit_information ?: 'Em definição', 'modal' => 'kit-modal', 'botao' => 'Ver kit completo'],
                            ['titulo' => 'Regulamento', 'valor' => $eventSetting->regulation ?: 'Em revisão', 'modal' => 'regulation-modal', 'botao' => 'Abrir regulamento'],
                        ] as $item)
                            <div class="rounded-md border border-zinc-200 bg-white p-5 shadow-sm shadow-zinc-950/5 transition hover:border-emerald-200">
                                <p class="text-sm font-semibold text-zinc-500">{{ $item['titulo'] }}</p>
                                @if ($item['modal'] === null)
                                    <p class="mt-2 whitespace-pre-line text-xl font-black">{{ $item['valor'] }}</p>
                                @else
                                    <p class="mt-2 text-sm font-semibold leading-6 text-zinc-700">
                                        Consulte os detalhes em uma janela rápida, sem sair da página.
                                    </p>
                                    <button type="button" data-modal-open="{{ $item['modal'] }}" class="mt-4 inline-flex rounded-md bg-emerald-800 px-4 py-2 text-sm font-black text-white transition hover:bg-emerald-900">
                                        {{ $item['botao'] }}
                                    </button>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </section>

            <section id="informacoes" class="mx-auto max-w-7xl px-5 py-16 sm:px-8">
                <div class="grid grid-cols-1 gap-8 lg:grid-cols-[1fr_0.85fr] lg:items-center">
                    <div>
                        <p class="text-sm font-bold uppercase tracking-normal text-emerald-700">Inscrições</p>
                        <h2 class="mt-3 text-3xl font-black leading-tight sm:text-4xl">Fluxo pronto para evoluir para pagamento online</h2>
                        <p class="mt-4 text-base leading-7 text-zinc-700">
                            Enquanto o meio de pagamento ideal está em análise, a página de inscrição organiza os dados do atleta e destaca que a confirmação será finalizada após a definição do gateway.
                        </p>
                    </div>

                    <div class="rounded-md bg-emerald-950 p-6 text-white shadow-xl shadow-emerald-950/15">
                        <p class="text-sm font-semibold text-lime-200">Próxima etapa</p>
                        <p class="mt-3 text-2xl font-black">Integrar pagamento</p>
                        <p class="mt-3 text-sm leading-6 text-white/78">
                            O formulário pode ser ligado a Pix, cartão ou plataforma externa quando o cliente escolher o provedor.
                        </p>
                        <a href="{{ route('registration') }}" class="mt-6 inline-flex rounded-md bg-lime-300 px-5 py-3 text-sm font-black text-emerald-950 transition hover:bg-lime-200">
                            Abrir inscrição
                        </a>
                    </div>
                </div>
            </section>
        </main>

        <footer class="border-t border-emerald-900/10 bg-white">
            <div class="mx-auto flex max-w-7xl flex-col gap-3 px-5 py-8 text-sm text-zinc-600 sm:px-8 md:flex-row md:items-center md:justify-between">
                <p class="font-semibold text-zinc-800">Clube de Desbravadores Ave Branca</p>
                <p>Corrida de rua com inscrições, modalidades e pagamento em preparação.</p>
            </div>
        </footer>

        <dialog id="kit-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-emerald-900/10 bg-white p-0 text-zinc-950 shadow-2xl shadow-emerald-950/30 backdrop:bg-emerald-950/75">
            <div class="flex items-start justify-between gap-5 border-b border-emerald-900/10 bg-emerald-950 p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-lime-200">Kit atleta</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Informações do kit</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f7faf2] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm shadow-emerald-950/5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->kit_information ?: 'Em definição') }}
                </div>
            </div>
        </dialog>

        <dialog id="regulation-modal" class="m-auto w-[min(44rem,calc(100vw-2rem))] rounded-md border border-emerald-900/10 bg-white p-0 text-zinc-950 shadow-2xl shadow-emerald-950/30 backdrop:bg-emerald-950/75">
            <div class="flex items-start justify-between gap-5 border-b border-emerald-900/10 bg-emerald-950 p-5 text-white sm:p-6">
                <div>
                    <p class="text-sm font-bold uppercase tracking-normal text-lime-200">Regulamento</p>
                    <h2 class="mt-1 text-2xl font-black leading-tight">Regras da corrida</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 bg-white/10 px-3 py-2 text-sm font-black text-white transition hover:bg-white/20" aria-label="Fechar modal">
                    Fechar
                </button>
            </div>
            <div class="max-h-[70vh] overflow-y-auto bg-[#f7faf2] p-4 sm:p-6">
                <div class="event-rich-content event-rich-content--modal rounded-md border border-emerald-900/10 bg-white p-5 shadow-sm shadow-emerald-950/5 sm:p-6">
                    {{ \Filament\Forms\Components\RichEditor\RichContentRenderer::make($eventSetting->regulation ?: 'Em revisão') }}
                </div>
            </div>
        </dialog>
    </body>
</html>
