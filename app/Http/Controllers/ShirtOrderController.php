<?php

namespace App\Http\Controllers;

use App\Actions\CreateShirtOrder;
use App\Http\Requests\StoreShirtOrderRequest;
use App\Models\Shirt;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
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

        DB::transaction(fn () => $action->handle($shirt, $data));

        return to_route('shirts.index')->with('status', 'Pedido de camiseta registrado com sucesso.');
    }
}
