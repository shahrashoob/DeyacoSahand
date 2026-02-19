<?php

namespace App\Models\LineProduct;

use App\Models\LineProduct\GoodsKind\GoodsKindLotNumberProperty;
use App\Models\LineProduct\GoodsKind\LotNumberProperty;
use App\Models\LineProduct\GoodsKind\GoodsKindLotNumberPropertyValue;
use App\Models\LineProduct\Machine\Machine;
use App\Models\User;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LotNumber extends Model {
    use HasFactory;
    use Loggable;
    use SoftDeletes;

    protected $fillable = [ "product_id", "code", "nosa_code","user_id" ];
    protected $table = "lot_numbers";


    public static function ExistsCode( $code, $product_id, $id = false ) {
        if ( $id ) {
            return LotNumber::where( [
                "product_id" => $product_id,
                "code"       => $code
            ] )->where( "id", "!=", $id )->
            exists();
        }

        return LotNumber::where( [ "product_id" => $product_id, "code" => $code ] )->exists();
    }

    public function getMachineLotEffectiveCode() {
        return Option::getFormatCode( $this->machine_lot_effective_code, 2 );
    }

    public function getLotEffective1Code() {
        return Option::getFormatCode( $this->lot_effective_code1, 3 );
    }

    public function getLotEffective2Code() {
        return Option::getFormatCode( $this->lot_effective_code2, 3 );
    }

    public function getLotEffective3Code() {
        return Option::getFormatCode( $this->lot_effective_code3, 3 );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function lot_number_property_value() {
        return
            $this->hasMany( GoodsKindLotNumberPropertyValue::class )->
            join( "goods_kind_lot_number_properties", "goods_kind_lot_number_properties.id", "goods_kind_lot_number_property_id" )->
            orderBy( "priority_number" );
    }

    public function worker() {
        return $this->belongsTo( User::class, "user_id" );
    }

    public function machine() {
        return $this->belongsTo( Machine::class );
    }

    public function lot_number_1() {
        return $this->belongsTo( LotNumber::class, "lot_number_1_id", "id" );
    }

    public function lot_number_2() {
        return $this->belongsTo( LotNumber::class, "lot_number_2_id", "id" );
    }

    public function lot_number_3() {
        return $this->belongsTo( LotNumber::class, "lot_number_3_id", "id" );
    }

    public function lot_number_4() {
        return $this->belongsTo( LotNumber::class, "lot_number_4_id", "id" );
    }

    public function lot_number_5() {
        return $this->belongsTo( LotNumber::class, "lot_number_5_id", "id" );
    }

    public function lot_number_6() {
        return $this->belongsTo( LotNumber::class, "lot_number_6_id", "id" );
    }

    public function lot_number_7() {
        return $this->belongsTo( LotNumber::class, "lot_number_7_id", "id" );
    }

    public function lot_number_8() {
        return $this->belongsTo( LotNumber::class, "lot_number_8_id", "id" );
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public function getPropertyValue( $goods_kind_lot_number_property_id, $type = "property_value", $null_if_not_set = true, $caption_show = true ) {
        switch ( $type ) {
            case "property_value":
                return GoodsKindLotNumberPropertyValue::where( [
                    "lot_number_id"                     => $this->id,
                    "product_id"                        => $this->product_id,
                    "goods_kind_lot_number_property_id" => $goods_kind_lot_number_property_id
                ] )->first();
                break;
            case "value":
                $item = GoodsKindLotNumberPropertyValue::where( [
                    "lot_number_id"                     => $this->id,
                    "product_id"                        => $this->product_id,
                    "goods_kind_lot_number_property_id" => $goods_kind_lot_number_property_id
                ] )->first();

                if ( $item ) {
                    return $item->value;
                }

                return null;
                break;

        }


    }

    public function isSetAllProperty() {

        $list                = GoodsKindLotNumberProperty::where( "goods_kind_id", $this->product->goods_kind_id )->get();
        $value_not_set_count = 0;
        if ( count( $list ) > 0 ) {
            foreach ( $list as $item ) {
                if ( ! LotNumberProperty::check_property_check_for_getting( $this, $item->lot_number_property_id ) ) {
                    $value_not_set_count ++;
                }

            }
        }

        return $value_not_set_count==0 ;

    }

    public static function checkLotProperty( LotNumber $lot_number, Product $product, $final_amount_packing_form, $weight ) {
        // گرم بر متر کالا برای کالاهایی است که یک واحد اصلی/فرعی آنها متر و واحد دیگر کیلوگرم باشد.
        if ( $product->sub_unit && (

                ( $product->unit->weight_conversion_rate != 0 && $product->sub_unit_id == 1100 ) ||
                ( $product->sub_unit->weight_conversion_rate != 0 && $product->unit_id == 1100 )
            )
        ) {

            $kgInMeterLot       = $lot_number->getPropertyValue( 1, "value" );
            $allowed_percentage = $product->goods_kind->allowed_percentage_in_lot_number_property / 100;
            $kgInMeter          = ( $weight ) / $final_amount_packing_form;
            // متر با وزن همخوانی ندارد، خطا می دهد.
            if ( $kgInMeter > ($kgInMeterLot+0) * ( 1 + $allowed_percentage ) || $kgInMeter < $kgInMeterLot * ( 1 - $allowed_percentage ) ) {


                $message = " وزن (" . $weight
                           . ") و " . $product->unit->caption . " (" . $final_amount_packing_form
                           . ") وارد شده برای بسته بندی با مقدار کیلوگرم بر متر طولی لات " . $lot_number->code .
                           " کالای " . $product->caption . " "
                           . " مطابقت ندارد.";

                return [ "result" => false, "error" => $message ];

            }
        }

        return [ "result" => true ];
    }
}
