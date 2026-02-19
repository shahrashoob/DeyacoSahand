<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeOutputBandPackingType extends Model
{
    use HasFactory;
    protected $table = "machine_type_output_band_packing_type";
    protected $fillable = [
        "machine_type_output_band_id",
        "goods_kind_id",
        "machine_type_id",
        "packing_type_id",
    ];
    public function packing_type(){
        return $this->belongsTo(PackingType::class);
    }
}
