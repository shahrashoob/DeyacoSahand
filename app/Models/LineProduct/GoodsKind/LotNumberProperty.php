<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\Utility\FieldType;
use App\Models\Utility\SpecialUnit;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LotNumberProperty extends Model
{
    use HasFactory;
    public function field_type() {
        return $this->belongsTo( FieldType::class );
    }

    public function special_unit() {
        return $this->belongsTo( SpecialUnit::class );
    }
    public function status() {
        return $this->belongsTo( Status::class );
    }

    public static function check_property_check_for_getting( $lot_number, $property_id ) {
        switch ( $property_id ) {
            case 1: // کیلوگرم بر متر کالا
                $lot_number_property_1 = $lot_number->getPropertyValue( 1, "value" );
                if ( ! $lot_number_property_1 && (
                        $lot_number->product->unit_id == 1100 ||
                        $lot_number->product->sub_unit_id == 1100

                    ) ) {
                    return false;
                }

                return true;
                break;
        }
        1 / 0;
    }
}
