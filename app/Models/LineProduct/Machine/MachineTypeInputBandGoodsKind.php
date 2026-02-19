<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeInputBandGoodsKind extends Model {
    use HasFactory;

    protected $table = "machine_type_input_band_goods_kind";
    protected $fillable = [
        "machine_type_input_band_id",
        "goods_kind_id",
        "send_product_request_form_by_robot",
        "allowing_raw_materials_to_be_injected_manually",
        "packing_form_in_return_raw_material_list",
        "warehouse_entry_confirmation_in_altogether",
    ];

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public function machine_type_input_band() {
        return $this->belongsTo( MachineTypeInputBand::class );
    }

    public function packing_type_list() {
        $packing_type_ids   = MachineTypeInputBandPackingType::where( [
            "machine_type_input_band_id" => $this->machine_type_input_band_id,
            "goods_kind_id"              => $this->goods_kind_id
        ] )->pluck( "packing_type_id" );
        $packing_type_ids[] = 0;

        return PackingType::whereIn( "id", $packing_type_ids )->get();
    }

    public static function getGoodsKindIdsWhereRequestFromRobot(
        $machine,
        $send_product_request_form_by_robot = 1,
        $allowing_raw_materials_to_be_injected_manually = 1,
        $goods_kind_id = null
    ) {

        return MachineTypeInputBandGoodsKind::
        join( "machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id" )->
        where( "machine_type_id", $machine->machine_type_id )->

        when( $send_product_request_form_by_robot, function ( $query ) use ( $send_product_request_form_by_robot ) {
            return $query->where( "send_product_request_form_by_robot", $send_product_request_form_by_robot );
        } )->
        where( "allowing_raw_materials_to_be_injected_manually", $allowing_raw_materials_to_be_injected_manually )->

        when( $goods_kind_id, function ( $query ) use ( $goods_kind_id ) {
            return $query->where( "goods_kind_id", $goods_kind_id );
        } )->

        pluck( "goods_kind_id" )->
        toArray();
    }
    public static function getGoodsKindIdsWhereAllowReturnToWarehouse(
        $machine,
        $packing_form_in_return_raw_material_list=1
    ) {

        return MachineTypeInputBandGoodsKind::
        join( "machine_type_input_bands", "machine_type_input_bands.id", "machine_type_input_band_id" )->
        where( "machine_type_id", $machine->machine_type_id )->
        where( "packing_form_in_return_raw_material_list", $packing_form_in_return_raw_material_list )->
        pluck( "goods_kind_id" )->
        toArray();
    }
}
