<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ $title }} | Ave Branca Run</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7fbff] text-zinc-950 antialiased">
        <main class="mx-auto grid min-h-screen max-w-2xl place-items-center px-5 py-10 sm:px-8">
            <section class="grid w-full gap-6 rounded-md border border-zinc-200 bg-white p-6 shadow-xl shadow-amber-950/10 sm:p-8">
                <header class="grid gap-2">
                    <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Pagamento</p>
                    <h1 class="text-3xl font-black">{{ $title }}</h1>
                    <p class="text-sm leading-6 text-zinc-600">O pagamento não está mais pendente. Nenhuma nova cobrança pode ser iniciada por este link.</p>
                </header>

                <dl class="grid gap-4 rounded-md border border-race-cyan/30 bg-amber-50 p-5">
                    <div class="grid gap-1">
                        <dt class="text-xs font-bold uppercase tracking-wide text-race-blue">Referência</dt>
                        <dd class="text-lg font-black text-race-ink">{{ $reference }}</dd>
                    </div>
                    <div class="grid gap-1">
                        <dt class="text-xs font-bold uppercase tracking-wide text-race-blue">Status atual</dt>
                        <dd class="text-2xl font-black text-race-ink">{{ $paymentStatus }}</dd>
                    </div>
                </dl>

                <a href="{{ $backUrl }}" class="inline-flex min-h-12 items-center justify-center rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:bg-race-night">
                    {{ $backLabel }}
                </a>
            </section>
        </main>
    </body>
</html>
