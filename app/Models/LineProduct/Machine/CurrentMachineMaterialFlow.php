<?php

namespace App\Models\LineProduct\Machine;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentMachineMaterialFlow extends Model
{
    use HasFactory;
    protected $table="current_machine_material_flows";
    protected $fillable=[
        "allocation_id",
        "product_id",
        "material_id",
        "input_band_id",
        "input_line_code",
        "goods_kind_id",
        "band_code",
    ];
}
