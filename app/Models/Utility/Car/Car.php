<?php

namespace App\Models\Utility\Car;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model {
    use HasFactory;

    protected $fillable = [
        "car_type_id",
        "driver_national_code",
        "driver_firstname",
        "driver_lastname",
        "driver_mobile",
        "car_plaque"
    ];

    public function car_type() {
        return $this->belongsTo( CarType::class );
    }
}
