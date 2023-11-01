<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingInformation extends Model {
    use HasFactory;

    protected $table = 'shipping_informations';

    protected $fillable = [
        'user_id',
        'receiver_name',
        'receiver_number_phone',
        'street',
        'village',
        'district',
        'regency',
        'province',
        'postal_code',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}
