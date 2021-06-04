<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\BitrixController;
use App\Http\Controllers\UDSController;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $orders = Order::all()->sortByDesc('id');
        return view('orders.index', compact('orders'));
    }
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(int $id)
    {
        $order = Order::findOrFail($id);

        $items = $order->items->map(function($item) {
            $item->sum = $item->pivot->qty * $item->pivot->price;
            $item->discount = 0;
            return $item;
        });

        $discount = round($order->points / $order->total * 100, 2);

        $items = $order->items->map(function($item) use ($discount) {
            $item->sum = round($item->pivot->qty * $item->pivot->price - ($item->pivot->qty * $item->pivot->price / 100 * $discount), 2);
            $item->discount = $item->pivot->qty * $item->pivot->price - $item->sum;
            return $item;
        });

        $sum = $items->reduce(function($acc, $item) {
            $acc += $item->sum;
            return $acc;
        });

        if ($sum !== $order->total - $order->points) {
            $lastElement = $items->count() - 1;
            $items[$lastElement]->sum += $order->total - $order->points - $sum;
            $items[$lastElement]->discount = $items[$lastElement]->pivot->qty * $items[$lastElement]->pivot->price - $items[$lastElement]->sum;
        }

        $sum = $items->reduce(function($acc, $item) {
            $acc += $item->sum;
            return $acc;
        });
        return view('orders.show', compact('order', 'sum', 'discount', 'items'));
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        $order->items()->detach();
        $order->delete();
        flash('Заказ удален')->success();
        return redirect()->route('orders.index');
    }
    public function updateItems($id)
    {
        $date      = new \DateTime();
        $companyId = env('COMPANY_ID');;
        $apikey    = env('APIKEY');
        $uuid      = uniqid();

        $response = Http::withHeaders([
                                       'Accept' => 'application/json',
                                       'X-Origin-Request-Id' => $uuid,
                                       'X-Timestamp' => $date->format(\DateTime::ATOM)
                                    ])
        ->withBasicAuth($companyId, $apikey)
        ->get("https://api.uds.app/partner/v2/goods-orders/{$id}/");

        $data = $response->json();

        $UDSController = new UDSController();
        $UDSController->saveItems($data['items']);

        $order = Order::find($id);
        $order->items()->detach();

        foreach ($data['items'] as $item) {
            $order->items()->attach($item['id'], ['price' => $item['price'], 'qty' => $item['qty']]);
        }
        $order->points = $data['points'];
        $order->total  = $data['total'];
        $order->save();

        $bitrixController = new BitrixController();
        $bitrixController->setItemsInOrder($order, $order->bitrix_id);
        flash('Заказ обновлен')->success();
        return redirect()->route('orders.index');
    }
}
