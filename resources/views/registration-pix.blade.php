<!DOCTYPE html>
<html lang="pt-BR">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Pagamento por Pix | Ave Branca Run</title>
        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-[#f7fbff] text-zinc-950 antialiased">
        <main class="mx-auto grid min-h-screen max-w-3xl place-items-center px-5 py-10 sm:px-8">
            <section class="w-full rounded-md border border-zinc-200 bg-white p-6 shadow-xl shadow-amber-950/10 sm:p-8">
                <p class="text-sm font-bold uppercase tracking-normal text-race-blue">Pagamento por Pix</p>
                <h1 class="mt-2 text-3xl font-black">Conclua sua inscrição</h1>
                <p class="mt-3 text-sm leading-6 text-zinc-600">Protocolo <strong>{{ $registration->protocol_number }}</strong>. Faça o Pix usando a chave abaixo e envie o comprovante.</p>

                @if (session('status'))
                    <div class="mt-6 rounded-md border border-emerald-200 bg-emerald-50 p-4 text-sm font-bold text-emerald-900">{{ session('status') }}</div>
                @endif

                <div class="mt-6 grid gap-5 rounded-md border border-race-cyan/30 bg-amber-50 p-5 sm:grid-cols-[1fr_auto] sm:items-center">
                    <div class="grid gap-4">
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-race-blue">Valor do Pix</p>
                            <p class="mt-1 text-3xl font-black text-race-ink">R$ {{ number_format($amount, 2, ',', '.') }}</p>
                        </div>
                        <div>
                            <p class="text-xs font-bold uppercase tracking-wide text-race-blue">Chave Pix</p>
                            <p class="mt-1 break-all text-lg font-black text-race-ink">{{ $pixKey }}</p>
                        </div>
                        <dl class="grid grid-cols-2 gap-x-4 gap-y-3 text-sm">
                            <div>
                                <dt class="font-bold text-zinc-500">Banco</dt>
                                <dd class="mt-1 font-black text-race-ink">{{ $pixBank }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zinc-500">Agência</dt>
                                <dd class="mt-1 font-black text-race-ink">{{ $pixAgency }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zinc-500">Conta</dt>
                                <dd class="mt-1 font-black text-race-ink">{{ $pixAccount }}</dd>
                            </div>
                            <div>
                                <dt class="font-bold text-zinc-500">Titular</dt>
                                <dd class="mt-1 font-black text-race-ink">{{ $pixAccountHolder }}</dd>
                            </div>
                        </dl>
                    </div>
                    <img src="{{ $pixQrCode }}" alt="QR Code Pix no valor de R$ {{ number_format($amount, 2, ',', '.') }}" class="mx-auto size-48 rounded-md bg-white p-2 sm:mx-0">
                    <div class="sm:col-span-2">
                        <label for="pix-payload" class="text-xs font-bold uppercase tracking-wide text-race-blue">Pix copia e cola</label>
                        <textarea id="pix-payload" rows="3" readonly class="mt-2 w-full resize-none rounded-md border border-zinc-300 bg-white px-3 py-2 text-xs text-zinc-700">{{ $pixPayload }}</textarea>
                    </div>
                </div>

                <div class="mt-6 rounded-md border border-amber-300 bg-amber-50 p-4 text-sm leading-6 text-amber-950" role="alert">
                    <strong class="font-black">Antes de finalizar:</strong> confirme no aplicativo do seu banco se o titular e os dados da conta recebedora correspondem às informações acima. Confira também o nome e o CPF do pagador antes de enviar o comprovante.
                </div>

                <form action="{{ request()->fullUrl() }}" method="POST" enctype="multipart/form-data" class="mt-6 grid gap-4">
                    @csrf
                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">Nome do pagador</span>
                        <input type="text" name="billing_name" value="{{ old('billing_name', $registration->billing_name) }}" autocomplete="name" class="rounded-md border border-zinc-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="Nome completo" required>
                        @error('billing_name')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">CPF do pagador</span>
                        <input type="text" name="billing_document" value="{{ old('billing_document', $registration->billing_document) }}" inputmode="numeric" data-mask="cpf" autocomplete="off" class="rounded-md border border-zinc-300 bg-white px-4 py-3 text-sm outline-none transition focus:border-race-cyan focus:ring-3 focus:ring-amber-100" placeholder="000.000.000-00" required>
                        @error('billing_document')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="grid gap-2">
                        <span class="text-sm font-bold text-zinc-800">Comprovante do Pix</span>
                        <input type="file" name="pix_receipt" accept=".jpg,.jpeg,.png,.pdf" class="rounded-md border border-zinc-300 bg-white px-4 py-3 text-sm" required>
                        <span class="text-xs text-zinc-500">JPG, PNG ou PDF, com até 5 MB.</span>
                        @error('pix_receipt')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    </label>
                    <label class="flex items-start gap-3 rounded-md border border-zinc-200 bg-zinc-50 p-4">
                        <input type="checkbox" name="payer_data_confirmed" value="1" class="mt-1 size-4 rounded border-zinc-300 text-race-blue focus:ring-race-cyan" required @checked(old('payer_data_confirmed'))>
                        <span class="text-sm font-semibold leading-6 text-zinc-800">Confirmo que conferi os dados do recebedor e que o nome e o CPF informados acima pertencem ao pagador do Pix.</span>
                    </label>
                    @error('payer_data_confirmed')<span class="text-sm font-semibold text-red-700">{{ $message }}</span>@enderror
                    <button type="submit" class="inline-flex min-h-12 items-center justify-center rounded-md bg-race-blue px-5 py-3 text-sm font-black text-white transition hover:bg-race-night">Enviar comprovante</button>
                </form>
            </section>
        </main>
    </body>
</html>
