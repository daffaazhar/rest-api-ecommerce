<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {
    use HasFactory;

    protected $table = 'orders';

    protected $fillable = [
        'user_id',
        'amount',
        'status',
        'shipping_information_id',
        'payment_method_id',
    ];

    public function setStatusAttribute($value) {
        $this->attributes['status'] = empty($value) ? 1 : $value;
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function orderItems() {
        return $this->hasMany(OrderItem::class);
    }

    public function shippingInformation() {
        return $this->belongsTo(ShippingInformation::class);
    }

    public function paymentMethod() {
        return $this->belongsTo(PaymentMethod::class);
    }
}
