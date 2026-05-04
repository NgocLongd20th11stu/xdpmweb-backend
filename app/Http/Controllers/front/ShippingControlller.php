<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;

class ShippingControlller extends Controller
{
    public function getShipping() {
        $shipping = ShippingCharge::first();
        return response()->json([
            'status' => 200,
            'data' => $shipping
        ],200);
    }
}
