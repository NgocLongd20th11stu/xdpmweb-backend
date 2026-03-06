<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProductController extends Controller
{
    //HÀM TRẢ VỀ TOÀN BỘ SẢN PHẨM
    public function index() {
        $product  = Product::orderBy('created_at','DESC')->get();
        return response()->json([
            'status' => 200,
            'data' => $product
        ],200);
    }


    
    //HÀM LƯU 1 SẢN PHẨM MỚI
    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|integer',
            'sku' => 'required|unique:products,sku',
            'is_featured' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' =>400,
                'errors' => $validator->errors()
            ],400);
        }
        
        //Lưu sản phẩm
        $product = new Product();
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->category_id = $request->category;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->barcode = $request->barcode;
        $product->save();

        //Lưu ảnh sản phẩm
        if(!empty($request->gallery)) {
            foreach($request->gallery as $key => $tempImageId) {
                $tempImage = TempImage::find($tempImageId);

                
                
                //Ảnh lớn
                $extArray = explode('.', $tempImage->name);
                $ext = end($extArray);


                $imageName = $product->id.'-'.time().'.'.$ext;
                
                $manager = new ImageManager(Driver::class);
                $img = $manager->read(public_path('upload/temp/'.$tempImage->name));
                $img->scaleDown(1200);
                $img->save(public_path('upload/product/large/'.$imageName));

                //Ảnh nhỏ
                $manager = new ImageManager(Driver::class);
                $img = $manager->read(public_path('upload/temp/'.$tempImage->name));
                $img->coverDown(400,450);
                $img->save(public_path('upload/product/small/'.$imageName));

                //Ảnh đầu tiên là ảnh đại diện
                if($key == 0) {
                    $product->image = $imageName;
                    $product->save();
                }
            }
        }

        return response()->json([
            'status' =>200,
            'message' => 'Đã thêm sản phẩm mới thành công.'
        ],200);
    }




    //HÀM TRẢ VỀ 1 SẢN PHẨM
    public function show($id) {
        $product = Product::find($id);

        if($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tim thấy sản phẩm'
            ],404);
        }

        return response()->json([
            'status' => 200,
            'data' => $product
        ],200);
    }



    //HÀM CẬP NHẬT SẢN PHẨM
    public function update($id, Request $request) {
        $product = Product::find($id);

        if($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tim thấy sản phẩm'
            ],404);
        }

        $validator = Validator::make($request->all(),[
            'title' => 'required',
            'price' => 'required|numeric',
            'category' => 'required|integer',
            'sku' => 'required|unique:products,sku,'.$id.',id',
            'is_featured' => 'required',
            'status' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' =>400,
                'errors' => $validator->errors()
            ],400);
        }

        //Cập nhật sản phẩm
        $product->title = $request->title;
        $product->price = $request->price;
        $product->compare_price = $request->compare_price;
        $product->category_id = $request->category;
        $product->brand_id = $request->brand;
        $product->sku = $request->sku;
        $product->quantity = $request->quantity;
        $product->description = $request->description;
        $product->short_description = $request->short_description;
        $product->status = $request->status;
        $product->is_featured = $request->is_featured;
        $product->barcode = $request->barcode;
        $product->save();

        //Lưu ảnh sản phẩm
        if(!empty($request->gallery)) {
            foreach($request->gallery as $key => $tempImageId) {
                $tempImage = TempImage::find($tempImageId);

                $extArray = explode('.',$tempImage->name);
                $ext = end($extArray);

                $imageName = $product->id.'-'.time().'.'.$ext;
                $manager = new ImageManager(Driver::class);
                $img = $manager->read(public_path('upload/temp/'.$tempImage->name));
                $img->scaleDown(1200);
                $img->save(public_path('upload/product/large/'.$imageName));


                
                $manager = new ImageManager(Driver::class);
                $img = $manager->read(public_path('upload/temp/'.$tempImage->name));
                $img->coverDown(400,460);
                $img->save(public_path('upload/product/small/'.$imageName));

                if($key == 0){
                    $product->image = $imageName;
                    $product->save();
                }
            }
        }

        return response()->json([
            'status' =>200,
            'message' => 'Đã cập nhật sản phẩm thành công.'
        ],200);
    }




    //HÀM XÓA SẢN PHẨM
    public function destroy($id) {
        $product = Product::find($id);

        if($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tim thấy sản phẩm'
            ],404);
        }

        $product->delete();
        return response()->json([
            'status' => 200,
            'message' => 'Đã xóa sản phẩm thành công.'
        ],200);
    }
}
