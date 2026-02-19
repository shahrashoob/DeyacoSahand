<?php

namespace App\Models\LineProduct\Machine\MachineType;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Machine\RawMaterialRequestAlgorithmType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineTypeInputAlgorithm extends Model {
    use HasFactory;

    protected $fillable = [
        "machine_type_id",
        "goods_kind_id",
        "raw_material_request_algorithm_type_id",
        "raw_material_request_sampling_algorithm_type_id",
        "dependency_to_other_goods_kind"
    ];
    protected $table = "machine_type_input_algorithm";

    public function machine_type() {
        return $this->belongsTo( MachineType::class );
    }

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public function raw_material_request_algorithm_type() {
        return $this->belongsTo( RawMaterialRequestAlgorithmType::class );
    }

    public function raw_material_request_sampling_algorithm_type() {
        return $this->belongsTo( RawMaterialRequestAlgorithmType::class );
    }

    public static function GetAlgorithm( $machine_type_id, $goods_kind_id ) {
        return self::where( [
            "machine_type_id" => $machine_type_id,
            "goods_kind_id"   => $goods_kind_id
        ] )->first();
    }

    /**
     * @param $machine_type_id
     * چک کردن اینکه به ازای همه رسته های کالایی، الگوریتم مشخص شده باشد.
     * @return bool
     */
    public static function CheckAlgorithmIsOK( $machine_type_id ) {
        $count_goods_kind = MachineTypeInputBandGoodsKind::where( "machine_type_id", $machine_type_id )->
        selectRaw( "count(distinct(id)) as goods_kind_count" )->first();

        $count_algorithm = MachineTypeInputAlgorithm::where( "machine_type_id", $machine_type_id )->count();

        if ( $count_algorithm != $count_goods_kind ) {
            return false;
        }

        return true;
    }
    public static function UpdateMachineTypeInputAlgorithm( MachineType $machine_type ) {
        foreach ( $machine_type->getGoodsKind() as $goods_kind_item ) {
            $machine_type_input_algorithm = MachineType\MachineTypeInputAlgorithm::
            where( "machine_type_id", $machine_type->id )->
            where( "goods_kind_id", $goods_kind_item->id )->
            first();
            if ( ! $machine_type_input_algorithm ) {
                MachineType\MachineTypeInputAlgorithm::
                create( [ "machine_type_id" => $machine_type->id, "goods_kind_id" => $goods_kind_item->id ] );
            }
        }
    }
}
