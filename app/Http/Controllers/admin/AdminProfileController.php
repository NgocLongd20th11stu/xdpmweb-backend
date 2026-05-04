<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;



class AdminProfileController extends Controller
{
    public function updateAdminProfile(Request $request) {
        // 1. Lấy thông tin admin hiện tại từ token
        $user = User::find($request->user()->id);

        if ($user == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy tài khoản admin!',
                'data' => []
            ], 404);
        }

        // 2. Validator chỉ cho name và email
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            // Unique email ngoại trừ ID của chính admin này
            'email' => 'required|email|unique:users,email,' . $user->id . ',id',
        ], [
            'name.required' => 'Họ tên không được để trống',
            'email.required' => 'Email không được để trống',
            'email.email' => 'Định dạng email không hợp lệ',
            'email.unique' => 'Email này đã được sử dụng bởi tài khoản khác',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400, // Hoặc 422 tùy theo chuẩn bạn đang dùng
                'errors' => $validator->errors()
            ], 400);
        }

        // 3. Cập nhật thông tin
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        return response()->json([
            'status' => 200,
            'message' => 'Cập nhật thông tin admin thành công',
            'data' => [
                'name' => $user->name,
                'email' => $user->email
            ]
        ], 200);
    }


    
    public function getAdminDetails(Request $request) {
    // Lấy thông tin admin hiện tại dựa trên token trong request
    $admin = User::find($request->user()->id);

    if (!$admin) {
        return response()->json([
            'status' => 404,
            'message' => 'Không tìm thấy tài khoản admin!',
            'data' => []
        ], 404);
    }

    // Trả về dữ liệu admin
    return response()->json([
        'status' => 200,
        'message' => 'Lấy thông tin thành công',
        'data' => [
            'id'    => $admin->id,
            'name'  => $admin->name,
            'email' => $admin->email,
            // Bạn có thể trả về thêm các trường khác nếu cần thiết cho UI admin
        ]
    ], 200);
}
}
