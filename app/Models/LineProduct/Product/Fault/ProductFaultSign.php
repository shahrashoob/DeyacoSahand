<?php

namespace App\Models\LineProduct\Product\Fault;

use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductFaultSign extends Model {
    use HasFactory;

    protected $table = "product_fault_signs";
    protected $fillable = [ "caption" ];


    public static function ExistsCaption(  $caption, $id = null ) {
        if ( $id ) {
            return ProductFaultSign::
            where( "caption", $caption )->
            where( "id", "!=", $id )->
            exists();
        }

        return ProductFaultSign::
        where( "caption", $caption )->
        exists();
    }

}
