<?php

namespace App\Models\LineProduct\Product;

use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductRoute extends Model {
    use HasFactory;

    protected $fillable = [ "product_id", "caption", "active_status_id","supply_type_id" ];

    public function active_status() {
        return $this->belongsTo( Status::class );
    }
    public function bom() {
        return $this->hasMany( BOM::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public static function exist( $product_id, $caption, $id = null ) {
        if ( $id ) {
            return ProductRoute::
            where( "product_id", $product_id )->
            where( "caption", $caption )->
            where( "id", "!=", $id )->
            exists();
        }

        return ProductRoute::
        where( "caption", $caption )->
        where( "product_id", $product_id )->
        exists();
    }

    public function code() {

        if ( $this->code ) {
            return $this->code;
        }
        $code = 1 + ProductRoute::where( "product_id", $this->product_id )->
            where( "id", "<", $this->id )->count();

        $code       = Str::of( $code )
                         ->when( $code < 10, function ( $string ) {
                             return Str::of( '0' )->append( $string );
                         } );
        $this->code = $code;
        $this->save();

        return $code;

    }

    public function fullCaption(){
        return $this->code()."- ". $this->caption;
    }

    public function line_product_station() {
        return $this->hasMany( LineProductStation::class, "product_route_id", "id" )->orderBy( "priority_number" );

    }
    public function first_line_product_station() {
        return $this->hasMany( LineProductStation::class, "product_route_id", "id" )->orderBy( "priority_number" )->first();

    }
}
