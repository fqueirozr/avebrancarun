<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loja | Itens avulsos</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-zinc-100 text-zinc-950">
    <main class="mx-auto grid max-w-5xl gap-8 px-4 py-10">
        <header class="grid gap-2">
            <a href="{{ route('home') }}" class="text-sm font-bold text-blue-700">← Voltar</a>
            <h1 class="text-3xl font-black">Itens avulsos</h1>
            <p class="text-zinc-600">Escolha um item e registre seu pedido separado da inscrição.</p>
        </header>

        @if (session('status'))
            <div class="rounded-lg border border-emerald-300 bg-emerald-50 p-4 font-bold text-emerald-800">{{ session('status') }}</div>
        @endif

        <form method="POST" action="{{ route('store.store') }}" class="grid gap-6 rounded-xl bg-white p-6 shadow-sm">
            @csrf
            <div class="grid gap-4 md:grid-cols-2">
                @forelse ($shirts as $shirt)
                    <label class="grid cursor-pointer gap-2 rounded-lg border border-zinc-200 p-4 has-checked:border-blue-600 has-checked:bg-blue-50">
                        <span class="flex items-start gap-3">
                            <input type="radio" name="shirt_id" value="{{ $shirt->id }}" @checked((int) old('shirt_id') === $shirt->id) required>
                            <span><strong>{{ $shirt->name }}</strong><br>R$ {{ number_format((float) $shirt->price, 2, ',', '.') }}</span>
                        </span>
                        @if ($shirt->description)<span class="text-sm text-zinc-600">{{ $shirt->description }}</span>@endif
                    </label>
                @empty
                    <p>Nenhum item avulso disponível no momento.</p>
                @endforelse
            </div>

            <div class="grid gap-4 md:grid-cols-2">
                <input name="customer_name" value="{{ old('customer_name') }}" placeholder="Nome completo" class="rounded-md border border-zinc-300 px-4 py-3" required>
                <input type="email" name="customer_email" value="{{ old('customer_email') }}" placeholder="E-mail" class="rounded-md border border-zinc-300 px-4 py-3" required>
                <input name="customer_phone" value="{{ old('customer_phone') }}" placeholder="Telefone (somente números)" inputmode="numeric" class="rounded-md border border-zinc-300 px-4 py-3" required>
                <select name="size" class="rounded-md border border-zinc-300 px-4 py-3" required>
                    <option value="">Tamanho</option>
                    @foreach (\App\Models\ParticipantRegistration::shirtSizeOptions() as $size)
                        <option value="{{ $size }}" @selected(old('size') === $size)>{{ $size }}</option>
                    @endforeach
                </select>
                <input type="number" name="quantity" value="{{ old('quantity', 1) }}" min="1" max="10" class="rounded-md border border-zinc-300 px-4 py-3" required>
            </div>

            @if ($errors->any())
                <ul class="grid gap-1 text-sm font-bold text-red-700">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            @endif

            <button class="rounded-md bg-blue-700 px-5 py-3 font-black text-white disabled:bg-zinc-400" @disabled($shirts->isEmpty())>Registrar pedido</button>
        </form>
    </main>
</body>
</html>
