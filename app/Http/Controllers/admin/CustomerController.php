<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getAllCustomers() {
    // Lấy tất cả user có role là 'customer' 
    $customers = User::where('role', 'customer')->orderBy('created_at', 'DESC')->get();

    return response()->json([
        'status' => 200,
        'data' => $customers
    ]);
}
}
