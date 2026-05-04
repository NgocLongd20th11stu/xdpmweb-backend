<?php

namespace App\Http\Controllers\front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    public function vnpay(Request $request) {
    
    // 1. Lấy thông tin từ .env và Request
        $vnp_Url = env('VNP_URL');
        $vnp_Returnurl = env('VNP_RETURN_URL');
        $vnp_TmnCode = env('VNP_TMN_CODE');
        $vnp_HashSecret = env('VNP_HASH_SECRET');

        $vnp_TxnRef = $request->order_id; // ID đơn hàng từ React gửi lên
        $vnp_OrderInfo = "Thanh toán đơn hàng #" . $vnp_TxnRef;
        $vnp_OrderType = "billpayment"; // Loại hàng hóa mặc định
        $vnp_Amount = $request->amount * 100; // VNPay yêu cầu nhân 100
        $vnp_Locale = "vn";
        $vnp_BankCode = "NCB"; // Có thể để trống hoặc gửi 'NCB'
        $vnp_IpAddr = $request->ip(); // Lấy IP người dùng

        // 2. Tạo mảng dữ liệu gửi đi
        $inputData = array(
            "vnp_Version" => "2.1.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_Returnurl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        // 3. Sắp xếp dữ liệu theo Alphabet (bắt buộc)
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        // 4. Tạo chữ ký bảo mật SHA512
        if (isset($vnp_HashSecret)) {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        // 5. Trả về link thanh toán dưới dạng JSON cho React
        return response()->json([
            'status' => 'success',
            'message' => 'Tạo link thanh toán thành công',
            'data' => $vnp_Url
        ]);
    }


    public function vnpayVerify(Request $request)
{
    $vnp_HashSecret = env('VNP_HASH_SECRET');
    $vnp_SecureHash = $request->vnp_SecureHash;

    $inputData = array();
    foreach ($request->all() as $key => $value) {
        if (substr($key, 0, 4) == "vnp_") {
            $inputData[$key] = $value;
        }
    }

    unset($inputData['vnp_SecureHash']);
    ksort($inputData);

    $i = 0;
    $hashData = "";
    foreach ($inputData as $key => $value) {
        if ($i == 1) {
            // SỬA Ở ĐÂY: Dùng urldecode để giá trị quay về nguyên bản
            $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
        } else {
            $hashData .= urlencode($key) . "=" . urlencode($value);
            $i = 1;
        }
    }

    $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);

    if ($secureHash === $vnp_SecureHash) {
        if ($request->vnp_ResponseCode == '00') {
            $order = \App\Models\Order::find($request->vnp_TxnRef);
            if ($order) {
                $order->payment_status = 'paid';
                $order->status = 'processing';
                $order->payment_method = 'vnpay';
                $order->save();
                return response()->json(['status' => 'success'], 200);
            }
        }
    }

    return response()->json([
        'status' => 'error',
        'message' => 'Chữ ký không khớp',
        'vnp_TxnRef' => $request->vnp_TxnRef,
        // Dòng này để Long check xem hash mình tính ra là gì
        'debug_my_hash' => $secureHash,
        'debug_vnp_hash' => $vnp_SecureHash
    ], 400);
}
}
