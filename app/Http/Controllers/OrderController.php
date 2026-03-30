<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class OrderController extends Controller
{
    public function print(Order $order) {
    return view('print.receipt', compact('order'));
}
}
