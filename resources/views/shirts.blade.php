<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="description" content="Loja de camisetas e itens avulsos da Ave Branca Run.">
        <title>Loja | Ave Branca Run</title>
        <link rel="icon" href="{{ asset('images/favicon-60-anos.png') }}" type="image/png">
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[radial-gradient(circle_at_top_left,_#e8f5ff_0,_#f7fbff_38%,_#f8fafc_100%)] text-zinc-950 antialiased">
        <header class="border-b border-white/10 bg-race-night text-white shadow-lg shadow-race-night/20">
            <nav class="mx-auto flex max-w-7xl items-center justify-between gap-4 px-5 py-4 sm:px-8" aria-label="Navegação principal">
                <a href="{{ route('home') }}">
                    <img src="{{ asset('images/ave-branca-run-logo.png') }}" alt="Ave Branca Run" class="h-12 w-auto max-w-48 object-contain">
                </a>
                <a href="{{ route('home') }}" class="rounded-md border border-white/15 bg-white/5 px-4 py-2 text-sm font-black transition hover:border-race-cyan hover:bg-race-cyan hover:text-race-night">Voltar ao site</a>
            </nav>
        </header>

        <main class="mx-auto grid max-w-7xl gap-8 px-5 py-10 sm:px-8 lg:py-14">
            <header class="max-w-3xl">
                <p class="text-xs font-black uppercase tracking-[0.18em] text-race-blue">Loja oficial</p>
                <h1 class="mt-2 text-4xl font-black tracking-tight text-race-night sm:text-5xl">Leve a corrida com você</h1>
                <p class="mt-4 text-base font-semibold leading-7 text-zinc-600">Escolha o item, informe obrigatoriamente o tamanho da camiseta e finalize seus dados.</p>
            </header>

            @if (session('status'))
                <div class="rounded-xl border border-emerald-300 bg-emerald-50 p-4 font-bold text-emerald-800">{{ session('status') }}</div>
            @endif
            @error('checkout')
                <div class="rounded-xl border border-red-300 bg-red-50 p-4 font-bold text-red-800">{{ $message }}</div>
            @enderror

            <form method="POST" action="{{ route('store.store') }}" class="grid gap-8 lg:grid-cols-[1.25fr_0.75fr] lg:items-start">
                @csrf

                <section class="grid gap-5">
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-sm font-black text-race-night">1. Escolha seu item</p>
                            <p class="mt-1 text-sm text-zinc-600">Clique em um produto para selecioná-lo.</p>
                        </div>
                        <span class="rounded-full bg-race-cyan/20 px-3 py-1 text-xs font-black text-race-blue">{{ $shirts->count() }} disponíveis</span>
                    </div>

                    <div class="grid gap-5 sm:grid-cols-2">
                        @forelse ($shirts as $shirt)
                            <label class="group relative grid cursor-pointer overflow-hidden rounded-2xl border border-race-blue/10 bg-white shadow-lg shadow-race-night/8 transition hover:-translate-y-1 hover:border-race-cyan has-checked:border-race-blue has-checked:ring-3 has-checked:ring-race-cyan/25">
                                <input type="radio" name="shirt_id" value="{{ $shirt->id }}" @checked((int) old('shirt_id') === $shirt->id) class="peer sr-only" required>
                                @if ($shirt->photo_path)
                                    <img src="{{ \Illuminate\Support\Facades\Storage::disk('public')->url($shirt->photo_path) }}" alt="Foto de {{ $shirt->name }}" class="aspect-[4/3] w-full bg-race-mist object-cover transition duration-500 group-hover:scale-105">
                                @else
                                    <div class="grid aspect-[4/3] place-items-center bg-linear-to-br from-race-mist via-white to-race-ice text-race-blue">
                                        <svg viewBox="0 0 24 24" fill="none" class="size-16" stroke="currentColor" stroke-width="1.4" aria-hidden="true"><path d="m8 4 4 2 4-2 5 3-3 5-2-1v9H8v-9l-2 1-3-5 5-3Z"/></svg>
                                    </div>
                                @endif
                                <span class="absolute right-3 top-3 hidden rounded-full bg-race-cyan px-3 py-1 text-xs font-black text-race-night peer-checked:block">Selecionado</span>
                                <span class="grid gap-3 p-5">
                                    <span class="flex items-start justify-between gap-4">
                                        <strong class="text-xl font-black text-race-night">{{ $shirt->name }}</strong>
                                        <strong class="shrink-0 text-lg font-black text-race-blue">R$ {{ number_format((float) $shirt->price, 2, ',', '.') }}</strong>
                                    </span>
                                    @if ($shirt->description)
                                        <span class="text-sm font-semibold leading-6 text-zinc-600">{{ $shirt->description }}</span>
                                    @endif
                                    @if ((float) $shirt->size_2xl_surcharge > 0 || (float) $shirt->size_3xl_surcharge > 0)
                                        <span class="text-xs font-semibold text-zinc-600">
                                            2XG: + R$ {{ number_format((float) $shirt->size_2xl_surcharge, 2, ',', '.') }}
                                            · 3XG: + R$ {{ number_format((float) $shirt->size_3xl_surcharge, 2, ',', '.') }}
                                        </span>
                                    @endif
                                </span>
                            </label>
                        @empty
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 font-semibold text-amber-950 sm:col-span-2">Nenhum item avulso disponível no momento.</div>
                        @endforelse
                    </div>
                    @error('shirt_id')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                </section>

                <aside class="grid gap-5 rounded-3xl border border-white/80 bg-white/95 p-5 shadow-2xl shadow-race-night/10 ring-1 ring-zinc-200/70 sm:p-7 lg:sticky lg:top-6">
                    <div>
                        <p class="text-xs font-black uppercase tracking-[0.16em] text-race-blue">2. Finalize o pedido</p>
                        <h2 class="mt-2 text-2xl font-black text-race-night">Seus dados</h2>
                    </div>

                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">Nome completo</span>
                        <input name="customer_name" value="{{ old('customer_name') }}" autocomplete="name" class="rounded-md border border-zinc-300 px-4 py-3 outline-none focus:border-race-cyan focus:ring-3 focus:ring-race-cyan/20" required>
                        @error('customer_name')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">CPF</span>
                        <input name="customer_cpf" value="{{ old('customer_cpf') }}" inputmode="numeric" data-mask="cpf" placeholder="000.000.000-00" class="rounded-md border border-zinc-300 px-4 py-3 outline-none focus:border-race-cyan focus:ring-3 focus:ring-race-cyan/20" required>
                        @error('customer_cpf')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">E-mail</span>
                        <input type="email" name="customer_email" value="{{ old('customer_email') }}" autocomplete="email" class="rounded-md border border-zinc-300 px-4 py-3 outline-none focus:border-race-cyan focus:ring-3 focus:ring-race-cyan/20" required>
                        @error('customer_email')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">Telefone</span>
                        <input name="customer_phone" value="{{ old('customer_phone') }}" inputmode="tel" data-mask="phone" placeholder="(00) 00000-0000" class="rounded-md border border-zinc-300 px-4 py-3 outline-none focus:border-race-cyan focus:ring-3 focus:ring-race-cyan/20" required>
                        @error('customer_phone')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <div class="grid gap-3">
                        <label class="grid gap-2">
                            <span class="text-sm font-bold text-zinc-800">Quantidade</span>
                            <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="10" data-shirt-quantity class="rounded-md border border-zinc-300 px-3 py-3 outline-none focus:border-race-cyan focus:ring-3 focus:ring-race-cyan/20" required>
                        </label>
                        <fieldset class="grid gap-3" data-shirt-sizes data-old-sizes='@json(old('sizes', ['']))' data-size-options='@json(array_keys(\App\Models\ParticipantRegistration::shirtSizeOptions()))'>
                            <legend class="text-sm font-bold text-zinc-800">Tamanho de cada camiseta</legend>
                            <div class="grid gap-3" data-shirt-size-list></div>
                        </fieldset>
                    </div>
                    @error('sizes')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    @error('sizes.*')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    @error('quantity')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror

                    <button type="button" data-modal-open="shirt-size-guide-modal" class="justify-self-start text-sm font-black text-race-blue underline decoration-race-cyan underline-offset-4">Consultar guia de medidas</button>

                    <button class="rounded-xl bg-race-blue px-5 py-3.5 font-black text-white shadow-lg shadow-race-blue/20 transition hover:-translate-y-0.5 hover:bg-race-night disabled:bg-zinc-400" @disabled($shirts->isEmpty())>Registrar pedido</button>
                </aside>
            </form>
        </main>

        <dialog id="shirt-size-guide-modal" class="m-auto max-h-[92vh] w-[min(72rem,calc(100vw-2rem))] overflow-hidden rounded-2xl bg-white p-0 shadow-2xl backdrop:bg-race-night/85">
            <div class="flex items-center justify-between gap-4 bg-race-night p-4 text-white sm:px-6">
                <div>
                    <p class="text-xs font-black uppercase tracking-wider text-race-cyan">Camisetas</p>
                    <h2 class="text-xl font-black">Guia de tamanhos e medidas</h2>
                </div>
                <button type="button" data-modal-close class="rounded-md border border-white/20 px-4 py-2 text-sm font-black">Fechar</button>
            </div>
            <div class="max-h-[78vh] overflow-auto bg-race-mist p-3 sm:p-6">
                <img src="{{ asset('images/guia-tamanhos-camisetas.png') }}" alt="Guia com medidas de tórax e comprimento das camisetas adultas e infantis" class="h-auto w-full rounded-xl bg-white">
            </div>
        </dialog>
    </body>
</html>
