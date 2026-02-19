<?php

namespace App\Models\LineProduct\Machine;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineProductPropertyValue extends Model {
    use HasFactory;

    protected $table = "machine_product_property_value";
    protected $fillable = [
        "machine_type_id",
        "product_id",
        "station_id",
        "machine_product_property_id",
        "station_sub_operation_id",
        "value"
    ];

    public static function GetCaption(Machine  $machine , Product $product,LineProductStation $line_product_station)
    {
        $property_list=[];
        foreach ($line_product_station->station->machine_product_property as $property) {
            $property_value = MachineProductPropertyValue::where([
                "machine_type_id" => $machine->machine_type_id,
                "product_id" => $product->id,
                "station_id" => $machine->station_id,
                "machine_product_property_id" => $property->id,
                "station_sub_operation_id" => $line_product_station->station_sub_operation_id
            ])->
            first();
            $value = $property_value->value??"";
            if ($property->field_type_id == 3) {
                $value=MachineProductPropertyOption::
                where("id", $value)->
                first();
                $value=$value->caption??"";
            }
            $property_list[]=[
                "caption" => $property->caption,
                "value" => $value,
                "property_value" => $property_value->value??"",
                "id"=>$property->id
            ];

        }

        return $property_list;
    }

}
