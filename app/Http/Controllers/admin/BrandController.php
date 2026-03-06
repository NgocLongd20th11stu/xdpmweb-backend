<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BrandController extends Controller
{
    // Hàm trả về tất cả thương hiệu
    public function index() {
        $brands = Brand::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => 200,
            'data' => $brands
        ]);
    }

    // Hàm lưu thương hiệu sản phẩm vào csdl
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ],400);
        }
        $brand = new Brand();
        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->save();

        return response()->json([
            'status' => 200,
            'message' => 'Thương hiệu đã được thêm thành công.',
            'data' => $brand
        ],200);
    }

    // Hàm trả về 1 thương hiệu sản phẩm
    public function show($id) {
        $brand = Brand::find($id);

        if ($brand == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thương hiệu sản phẩm!',
                'data' => []
            ],404);
        }
        return response()->json([
            'status' => 200,
            'data' => $brand
        ]);
    }

    // Hàm cập nhật 1 thương hiệu sản phẩm
    public function update($id, Request $request) {
        $brand = Brand::find($id);

        if ($brand == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thương hiệu sản phẩm!',
                'data' => []
            ],404);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ],400);
        }
       
        $brand->name = $request->name;
        $brand->status = $request->status;
        $brand->save();

        return response()->json([
            'status' => 200,
            'message' => 'Thương hiệu đã cập nhật thành công.',
            'data' => $brand
        ],200);
    }

    // Hàm xóa 1 thương hiệu sản phẩm
    public function destroy($id) {
        $brand = Brand::find($id);

        if ($brand == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy thương hiệu sản phẩm!',
                'data' => []
            ],404);
        }

        $brand->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Thương hiệu đã xóa thành công.',
            
        ],200);
    }
}
