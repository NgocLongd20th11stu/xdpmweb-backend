<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats() {
        // Đếm số lượng từ các bảng tương ứng
        $usersCount = User::where('role', 'customer')->count(); // Giả sử bạn có cột role
        $ordersCount = Order::count();
        $productsCount = Product::count();

        return response()->json([
            'status' => 200,
            'users' => $usersCount,
            'orders' => $ordersCount,
            'products' => $productsCount
        ]);
    }
}
