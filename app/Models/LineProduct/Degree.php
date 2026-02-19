<?php

namespace App\Models\LineProduct;

use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Degree extends Model {
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "caption",
        "goods_kind_id",
        "warehouse_id",
        "percent_of_price_reduction",
        "degree_type_id",
        "active_status_id"
    ];


    public static function Exists( $caption, $goods_kind_id, $id = false ) {
        if ( $id ) {
            return Degree::where( [
                "caption"       => $caption,
                "goods_kind_id" => $goods_kind_id
            ] )->where( "id", "!=", $id )->exists();
        }

        return Degree::where( [ "caption" => $caption, "goods_kind_id" => $goods_kind_id ] )->exists();
    }

    public function getMasterDegree( $goods_kind_id ) {

        $result=Degree::getMainDegree($goods_kind_id);

        if ( $result["result"] ) {
            return $result["degree"];
        }
        // درجه اصلی برای رسته کالایی تعریف نشده است.
        1 / 0;
    }
    public static function getMainDegree( $goods_kind_id ) {
        $main_degree = Degree::where( [
            "degree_type_id"   => 1, // درجه اصلی
            "goods_kind_id"    => $goods_kind_id,
            "active_status_id" => 1200, // فعال
        ] )->first();
        if ( ! $main_degree ) {
            return [
                "result" => false,
                "error"  => "درجه اصلی در رسته کالایی با کد $goods_kind_id مشخص نشده است."
            ];

        }

        return [ "result" => true, "degree" => $main_degree ];
    }

    public function fullCaption() {
        return $this->code . " - " . $this->caption;
    }

    public function getCode() {
//        if ( $this->code != "" ) {
//            return $this->code;
//        }
        $code_number = Degree::where( "goods_kind_id", $this->goods_kind_id )->where( "id", "<", $this->id )->count() + 1;
        if ( $code_number < 10 ) {
            $this->code = "0" . $code_number;
        } else {
            $this->code = $code_number;
        }

        $this->save();

        return $this->code;
    }

    public static function getCountMasterDegree( $goods_kind_id ) {
        return Degree::
        where( "goods_kind_id", $goods_kind_id )->
        where( "degree_type_id", 1 )->count();
    }

    public function warehouse() {
        return $this->belongsTo( Warehouse::class );
    }
    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public function degree_type() {
        return $this->belongsTo( DegreeType::class );
    }

    public function active_status() {
        return $this->belongsTo( Status::class );
    }

}
