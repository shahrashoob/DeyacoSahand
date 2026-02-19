<?php

namespace App\Models\LineProduct\Packing;

use App\Models\LineProduct\Carrier\CarrierType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingTypeLayer extends Model
{
    use HasFactory;
    protected $fillable=["packing_type_id","layer_code","carrier_type_id"];

    public function packing_type(){
        return $this->belongsTo(PackingType::class);
    }
    public function carrier_type(){
        return $this->belongsTo(CarrierType::class);
    }
}
