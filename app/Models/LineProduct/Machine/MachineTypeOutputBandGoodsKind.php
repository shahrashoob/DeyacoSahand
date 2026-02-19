<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Utility\SmartObject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeOutputBandGoodsKind extends Model {
    use HasFactory;

    protected $table = "machine_type_output_band_goods_kind";
    protected $fillable = [
        "machine_type_output_band_id",
        "goods_kind_id",
        "effect_is_shared",
        "machine_type_calculation_method_for_unit_id",
        "machine_type_calculation_method_for_sub_unit_id",
        "machine_type_calculation_method_for_sub_unit2_id",
        "smart_object_id_for_unit",
        "smart_object_id_for_sub_unit",
        "smart_object_id_for_sub_unit2",
    ];

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public function machine_type_output_band() {
        return $this->belongsTo( MachineTypeOutputBand::class );
    }

    public function packing_type_list() {
        $packing_type_ids   = MachineTypeOutputBandPackingType::where( [
            "machine_type_output_band_id" => $this->machine_type_output_band_id,
            "goods_kind_id"               => $this->goods_kind_id
        ] )->pluck( "packing_type_id" );
        $packing_type_ids[] = 0;

        return PackingType::whereIn( "id", $packing_type_ids )->get();
    }
    public function smart_object_for_unit(){
        return $this->belongsTo(SmartObject::class,"smart_object_id_for_unit");
    }
    public function smart_object_for_sub_unit(){
        return $this->belongsTo(SmartObject::class,"smart_object_id_for_sub_unit");
    }
    public function smart_object_for_sub_unit2(){
        return $this->belongsTo(SmartObject::class,"smart_object_id_for_sub_unit2");
    }
}
