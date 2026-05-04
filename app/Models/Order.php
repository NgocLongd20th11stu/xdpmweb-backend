<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id', 'name', 'email', 'address', 'mobile', 'state', 'zip', 'city',
        'grand_total', 'subtotal', 'discount', 'shipping', 
        'payment_status', 'status', 'payment_method' 
    ];
    public function items() {
        return $this->hasMany(OrderItem::class);
    }

    protected function casts(): array
    {
        return [
            'created_at' => 'datetime:d/m/Y',
        ];
    }
}
