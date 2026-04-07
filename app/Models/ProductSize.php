<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    // Định nghĩa quan hệ ngược lại với model Size
    public function size()
    {
        // 'size_id' là tên cột khóa ngoại trong bảng product_sizes của bạn
        return $this->belongsTo(Size::class, 'size_id');
    }
}
