<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\GoodsKind;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class CarrierTypeController extends Controller {
    public function index( GoodsKind $goods_kind ) {

        $carrier_type_option = Option::get( "carrier_type" );

        return view( "line_product_station.carrier_type.index", compact( "carrier_type_option", "goods_kind" ) );
    }

    public function store( Request $request, GoodsKind $goods_kind ) {
        $carrier_type = CarrierType::find( $request->carrier_type_id );
        if ( $carrier_type ) {
            $goods_kind->carrier_type()->attach( $carrier_type->id );
            return back()->with( ["success"=>"افزودن با موفقیت انجام شد."] );
        }


    }

    public function delete( GoodsKind $goods_kind, CarrierType $carrier_type ) {
        $goods_kind->carrier_type()->detach( $carrier_type->id );

        return back()->withErrors( "حذف با موفقیت انجام شد." );
    }
}
