<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CurrentMachineInputOutputBand extends Model {
    use HasFactory;
    use Loggable;
    protected $table = "current_machine_input_output_bands";
    protected $fillable = [
        "production_id",
        "product_id",
        "lot_number_id",
        "machine_type_id",
        "machine_id",
        "input_band_id",
        "input_line_code",
        "band_code",
        "material_id",
        "number",
        "amount",
        "percent_of_use",
        "effect_is_shared",
        "allocation_id",
        "goods_kind_id"
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function carrier() {
        return $this->belongsTo( Carrier::class );
    }
    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }

    public function material() {
        return $this->belongsTo( Product::class, "material_id", "id" );
    }

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public function input_band() {
        return $this->belongsTo( MachineTypeInputBand::class );
    }

    public function getBandCodeList() {

        if ( $this->effect_is_shared ) {
            $list = CurrentMachineInputOutputBand::where( [
                "allocation_id" => $this->allocation_id,
            ] )->
            where( "band_code", "!=", 0 )->
            groupBy( "band_code" )->
            pluck( "band_code" );
            $text = "";
            foreach ( $list as $item ) {
                $text .= $item . ", ";
            }

            return rtrim( $text, ", " );
        } else {
            return $this->band_code;
        }
    }

    public static function createNew(
        $reserve_production_id,
        $product_id,
        $lot_number_id,
        $machine_type_id,
        $machine_id,
        $input_band_id,
        $input_line_code,
        $band_code,
        $material_id,
        $number,
        $amount,
        $percent_of_use,
        $effect_is_shared,
        $goods_kind_id,
        $allocation_id
    ) {
        return CurrentMachineInputOutputBand::create( [

            "production_id"    => $reserve_production_id,
            "product_id"       => $product_id,
            "lot_number_id"    => $lot_number_id,
            "machine_type_id"  => $machine_type_id,
            "machine_id"       => $machine_id,
            "input_band_id"    => $input_band_id,
            "input_line_code"  => $input_line_code,
            "band_code"        =>  $band_code,
            "material_id"      => $material_id,
            "number"           => $number,
            "amount"           => $amount,
            "percent_of_use"   => $percent_of_use,
            "effect_is_shared" => $effect_is_shared,
            "goods_kind_id" => $goods_kind_id,
            "allocation_id"    => $allocation_id,
        ] );
    }
}
