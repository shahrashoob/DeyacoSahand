<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Utility\FieldType;
use App\Models\Utility\SpecialUnit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineProductProperties extends Model {
    use HasFactory;

    public function field_type() {
        return $this->belongsTo( FieldType::class );
    }


    public function special_unit() {
        return $this->belongsTo( SpecialUnit::class );
    }

    public function getValue( $machine_type_id, $product_id, $station_sub_operation_id ) {

        $property_value = MachineProductPropertyValue::where( [
            "machine_type_id"             => $machine_type_id,
            "product_id"                  => $product_id,
            "machine_product_property_id" => $this->id,
        ] )->
        when( $station_sub_operation_id, function ( $query ) use ( $station_sub_operation_id ) {

            return $query->where( "station_sub_operation_id", $station_sub_operation_id );
        } )->
        first();
        if ( ! $property_value ) {
            return null;
        }

        return $property_value->value;
    }

    public static function getPropertyValue( MachineProductProperties $machine_product_properties, $machine_type_id, $product_id, $station_sub_operation_id ) {
        return $machine_product_properties->getValue( $machine_type_id, $product_id, $station_sub_operation_id );
    }
}
