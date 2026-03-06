<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Hàm trả về tất cả danh mục sản phẩm
    public function index() {
        $categories = Category::orderBy('created_at', 'DESC')->get();
        return response()->json([
            'status' => 200,
            'data' => $categories
        ]);
    }

    // Hàm lưu danh mục sản phẩm vào csdl
    public function store(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'status' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ],400);
        }
        $category = new Category();
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

        return response()->json([
            'status' => 200,
            'message' => 'Danh mục đã được thêm thành công.',
            'data' => $category
        ],200);
    }

    // Hàm trả về 1 danh mục sản phẩm
    public function show($id) {
        $category = Category::find($id);

        if ($category == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy danh mục sản phẩm!',
                'data' => []
            ],404);
        }
        return response()->json([
            'status' => 200,
            'data' => $category
        ]);
    }

    // Hàm cập nhật 1 danh mục sản phẩm
    public function update($id, Request $request) {
        $category = Category::find($id);

        if ($category == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy danh mục sản phẩm!',
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
       
        $category->name = $request->name;
        $category->status = $request->status;
        $category->save();

        return response()->json([
            'status' => 200,
            'message' => 'Danh mục đã cập nhật thành công.',
            'data' => $category
        ],200);
    }

    // Hàm xóa 1 danh mục sản phẩm
    public function destroy($id) {
        $category = Category::find($id);

        if ($category == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy danh mục sản phẩm!',
                'data' => []
            ],404);
        }

        $category->delete();

        return response()->json([
            'status' => 200,
            'message' => 'Danh mục đã xóa thành công.',
            
        ],200);
    }
}
