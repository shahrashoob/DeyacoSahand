<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\Utility\SpecialUnit;
use Illuminate\Http\Request;

class SpecialUnitController extends Controller {
    //

    private $view_path = "utility.special_unit.";
    private $route_path = "utility.special_unit.";

    public function create( GoodsKind $goods_kind ) {
        $list = SpecialUnit::all();

        return view( $this->view_path . "create", compact( "list", "goods_kind" ) );
    }

    public function store( Request $request ) {
//        if ( $request->caption == "" || SpecialUnit::Exists( $request->caption ) ) {
//            return back()->withErrors( "کد  تکراری است" );
//        }
//        SpecialUnit::create( $request->all() );
//
//        return back()->with( [ "success" => "واحد با موفقیت اضافه شد" ] );
        return back()->withErrors( "امکان افزودن واحد اختصاصی برای شما وجود ندارد، لطفا با پشتیبانی تماس بگیرید." );

    }


}
