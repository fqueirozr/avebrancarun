<?php

namespace App\Http\Controllers;

use App\Actions\CreateShirtOrder;
use App\Http\Requests\StoreShirtOrderRequest;
use App\Mail\ShirtOrderReceived;
use App\Models\Shirt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class ShirtOrderController extends Controller
{
    public function index(): View
    {
        return view('shirts', ['shirts' => Shirt::query()->where('is_active', true)->orderBy('name')->get()]);
    }

    public function store(StoreShirtOrderRequest $request, CreateShirtOrder $action): RedirectResponse
    {
        $data = $request->validated();
        $shirt = Shirt::query()->findOrFail($data['shirt_id']);
        unset($data['shirt_id']);

        $shirtOrder = DB::transaction(fn () => $action->handle($shirt, $data));
        $shirtOrder->load('shirt');

        Mail::to($shirtOrder->customer_email)->send(new ShirtOrderReceived($shirtOrder));

        return to_route('store.index')->with('status', 'Pedido de item avulso registrado com sucesso. O recibo foi enviado por e-mail.');
    }
}
