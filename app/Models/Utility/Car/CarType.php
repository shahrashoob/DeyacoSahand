<?php

namespace App\Models\Utility\Car;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarType extends Model
{
    use HasFactory;
    protected $fillable=["min_weight","max_weight","min_volume","max_volume"];
}
