<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function saveOrder(Request $request) {

        if(!empty($request->cart)) {

            // Lưu đơn hàng vào database
            $order = new Order();
            $order->name = $request->name;
            $order->email = $request->email;
            $order->address = $request->address;
            $order->mobile = $request->mobile;
            $order->state = $request->state;
            $order->zip = $request->zip;
            $order->city = $request->city;
            $order->grand_total = $request->grand_total;
            $order->subtotal = $request->sub_total;
            $order->discount = $request->discount;
            $order->shipping = $request->shipping;
            $order->payment_status = $request->payment_status;
            $order->status = $request->status;

            $order->user_id = $request->user()->id;
            $order->save();

            // Lưu chi tiết đơn hàng
            foreach ($request->cart as $item) {
                $orderItem = new OrderItem();
                $orderItem->order_id = $order->id;
                $orderItem->price = $item['quantity'] * $item['price'];
                $orderItem->unit_price = $item['price'];
                $orderItem->quantity = $item['quantity'];
                $orderItem->product_id = $item['product_id'];
                $orderItem->size = $item['size']; 
                $orderItem->name = $item['title'];
                $orderItem->save();
            }
            return response()->json([
                    'status' => 200,
                    'id' => $order->id,
                    'message' => 'Đã đặt hàng thành công'
                ],200);
        } else {
            return response()->json([
                'status' => 400,
                'message' => 'Giỏ hàng trống!'
            ],400);
        }  
    }
}
