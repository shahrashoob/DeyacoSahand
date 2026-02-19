<?php

namespace App\Models\Utility;

use App\Models\LineProduct\Product;
use App\Models\Utility\Unit\UnitType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model {
    use HasFactory;
// این کلاس باید به پوشه Utility/Unit انتقال یابد
    public static function GetIdFromCaption( $caption ) {

        $unit = Unit::where( "search_caption", "like", "%" . $caption . "%" )->first();

        return isset( $unit ) ? $unit->id : - 100;

    }

    public function carton_name() {
        if ( $this->id == 300 ) {
            return "کیلوگرم";
        } else {
            return "کارتن";
        }
    }

    public static function HasWeightUnit( Product $product ) {
        if ( $product->unit->weight_conversion_rate > 0 ) {
            return true;
        }
        if ( $product->sub_unit && $product->sub_unit->weight_conversion_rate > 0 ) {
            return true;
        }

        return false;
    }

    /**
     * @param Product $product
     * @param $unit_type_id
     * @return bool
     */
    public static function CheckUnitForProduct(Product $product, $unit_type_id){
        switch ($unit_type_id){
            case 1:
                return $product->unit->id??false;
                break;
            case 2:
                return $product->sub_unit->id??false;
                break;
            case 3:
                return $product->sub_unit2->id??false;
                break;
        }
        return false;
    }
}
