<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\TempImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class TempImageController extends Controller
{
    //Hàm lưu hình ảnh sản phẩm tạm thời
    public function store(Request $request) {
        $validator = Validator::make($request->all(),[
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);

        if($validator->fails()) {
            return response()->json([
                'status' => 400,
                'errors' => $validator->errors()
            ],400);
        }

        //Lưu ảnh
        $tempImage = new TempImage();
        $tempImage->name = 'Tên tạm';
        $tempImage->save();

        $image = $request->file('image');
        $imageName = time().'.'.$image->extension();
        $image->move(public_path('upload/temp'),$imageName);

        $tempImage->name = $imageName;
        $tempImage->save();

        //Lưu ảnh dạng nhỏ
        $manager = new ImageManager(Driver::class);
        $img = $manager->read(public_path('upload/temp/'.$imageName));
        $img->coverDown(400, 450);
        $img->save(public_path('upload/temp/thumb/'.$imageName));

        return response()->json([
            'status' => 200,
            'message' => 'Ảnh đã được tải lên',
            'data' => $tempImage
        ],200);
    }
}
