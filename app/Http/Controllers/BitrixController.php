<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Bitrix\CRest;
use App\Models\Order;

class BitrixController extends Controller
{
    public function upload(int $id)
    {
        $order = Order::findOrFail($id);
        return redirect()->route('orders.index');
    }
}
