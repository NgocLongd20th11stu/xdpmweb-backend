<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductSize;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Exception;
use Illuminate\Support\Facades\File;
class ProductController extends Controller
{
    //HÀM TRẢ VỀ TOÀN BỘ SẢN PHẨM
    public function index() {
        $product  = Product::orderBy('created_at','DESC')
                    ->with(['product_images','product_sizes'])
                    ->get();
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


        if(!empty($request->sizes)) {
            foreach($request->sizes as $sizeId) {
                $productSize = new ProductSize();
                $productSize->size_id = $sizeId;
                $productSize->product_id = $product->id;
                $productSize->save();
            }
        }


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

                $productImage = new ProductImage();
                $productImage->image = $imageName;
                $productImage->product_id = $product->id;
                $productImage->save();

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
        $product = Product::with(['product_images','product_sizes'])
                    ->find($id);

        if($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tim thấy sản phẩm'
            ],404);
        }

        $productSizes = $product->product_sizes()->pluck('size_id');

        return response()->json([
            'status' => 200,
            'data' => $product,
            'productSizes' => $productSizes
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

        if(!empty($request->sizes)) {
            ProductSize::where('product_id', $product->id)->delete();
            foreach($request->sizes as $sizeId) {
                $productSize = new ProductSize();
                $productSize->size_id = $sizeId;
                $productSize->product_id = $product->id;
                $productSize->save();
            }
        }

        return response()->json([
            'status' =>200,
            'message' => 'Đã cập nhật sản phẩm thành công.'
        ],200);
    }



    //HÀM XÓA SẢN PHẨM
    public function destroy($id) {
        $product = Product::with('product_images')->find($id);

        if($product == null) {
            return response()->json([
                'status' => 404,
                'message' => 'Không tim thấy sản phẩm'
            ],404);
        }

        $product->delete();

        if($product->product_images()) {
            foreach($product->product_images() as $productImage) {
                File::delete(public_path('upload/product/large/'.$productImage->image));
                File::delete(public_path('upload/product/small/'.$productImage->image));
            }
        }

        return response()->json([
            'status' => 200,
            'message' => 'Đã xóa sản phẩm thành công.'
        ],200);
    }

    

    public function saveProductImage(Request $request) {
        $validator = Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'product_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ], 400);
        }

        try {
            $image = $request->file('image');
            $imageName = $request->product_id . '-' . time() . '.' . $image->getClientOriginalExtension();

            // Khởi tạo Manager 1 lần duy nhất
            $manager = new ImageManager(new Driver());

            // Đảm bảo thư mục tồn tại (Tránh lỗi 500 do thiếu folder)
            $largePath = public_path('upload/product/large/');
            $smallPath = public_path('upload/product/small/');
            if (!file_exists($largePath)) mkdir($largePath, 0775, true);
            if (!file_exists($smallPath)) mkdir($smallPath, 0775, true);

            // Xử lý ảnh - Dùng $image trực tiếp thay vì getPathName()
            $img = $manager->read($image); 

            // Lưu ảnh lớn
            $img->scaleDown(1200)->save($largePath . $imageName);

            // Lưu ảnh nhỏ (Lưu ý: dùng clone để không làm hỏng đối tượng gốc nếu cần dùng tiếp)
            $img->coverDown(400, 450)->save($smallPath . $imageName);

            // Lưu vào Database
            $productImage = new ProductImage();
            $productImage->image = $imageName;
            $productImage->product_id = $request->product_id;
            $productImage->save();

            return response()->json([
                'status' => 200,
                'message' => 'Ảnh đã được tải lên',
                'data' => $productImage
            ], 200);

        } catch (Exception $e) {
            // Trả về lỗi cụ thể để bạn biết lỗi ở đâu (GD, quyền ghi file, hay decode)
            return response()->json([
                'status' => 500,
                'message' => 'Lỗi xử lý ảnh: ' . $e->getMessage()
            ], 500);
        }
    }

    //Hàm thay đổi ảnh đại diện sản phẩm
    public function updateDefaultImage(Request $request) {
        $product = Product::find($request->product_id);
        $product->image = $request->image;
        $product->save();
        return response()->json([
                'status' => 200,
                'message' => 'Ảnh đại diện sản phẩm đã thay đổi thành công',
            ], 200);
    }

    //Hàm xóa ảnh sản phẩm
    public function deleteProductImage($id) {
        $productImage = ProductImage::find($id);

        if($productImage == null){
            return response()->json([
                'status' => 404,
                'message' => 'Không tìm thấy ảnh!',
            ], 404);
        }
        File::delete(public_path('upload/product/large/'.$productImage->image));
        File::delete(public_path('upload/product/small/'.$productImage->image));

        $productImage->delete();

        return response()->json([
                'status' => 200,
                'message' => 'Ảnh đã được xóa',
            ], 200);
    }

}
