<?php

namespace App\Http\Controllers\LineProductStation\GoodsKind;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class GoodsKindClassificationController extends Controller {
    private $view_path = "line_product_station.goods_kind.classification.";
    private $route_path = "line_product_station.goods_kind.classification.";

    public function index( GoodsKind $goods_kind ) {

        return view( $this->view_path . "index", compact( "goods_kind" ) );
    }

    public function create( GoodsKind $goods_kind ) {

        return view( $this->view_path . "create", compact( "goods_kind" ) );
    }

    public function store( Request $request, GoodsKind $goods_kind ) {
        if ( $request->caption == "" || GoodsKindClassification::ExistsCaption( $request->caption, $goods_kind ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }

        if($goods_kind->classification()->count()==0){
            $request["goods_kind_classification_type_id"]=1;// اصلی
        }else{
            $request["goods_kind_classification_type_id"]=2;// فرعی
        }

        $request["goods_kind_id"] = $goods_kind->id;
        GoodsKindClassification::create( $request->all() );

        return redirect()->route( $this->route_path . "index", $goods_kind )->with( [ "success" => "طبقه با موفقیت اضافه شد" ] );

    }

    public function edit(GoodsKindClassification $goods_kind_classification ) {


        return view( $this->view_path . "edit", compact( "goods_kind_classification" ) );

    }

    public function update( Request $request,GoodsKindClassification $goods_kind_classification ) {

        if ( $request->caption == "" || GoodsKindClassification::ExistsCaption( $request->caption, $goods_kind_classification->id ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }
        $goods_kind_classification->update( $request->all() );

        return redirect()->route( $this->route_path . "index", $goods_kind_classification->goods_kind_id )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }

    public function destroy( GoodsKindClassification $goods_kind_classification  ) {

        if ( $goods_kind_classification->goods_kind_classification_type_id==1  ) {
            return back()->withErrors( "امکان حذف طبقه بندی اصلی وجود ندارد" );
        }
        if (
            GoodsKind\GoodsKindClassificationProduct::where( "goods_kind_classification_id", $goods_kind_classification->id )->exists()
        ) {
            return back()->withErrors( "به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد" );
        }
        $goods_kind_id = $goods_kind_classification->goods_kind_id;
        $goods_kind_classification->delete();

        return redirect()->route( $this->route_path . "index", $goods_kind_id )->with( [ "success" => "یک آیتم با موفقیت حذف گردید" ] );

    }

    public function change_classification_type (GoodsKindClassification $goods_kind_classification ) {

        GoodsKindClassification::
        where("goods_kind_id",$goods_kind_classification->goods_kind_id)->
        update(["goods_kind_classification_type_id"=>2]);

        $goods_kind_classification->goods_kind_classification_type_id=1;
        $goods_kind_classification->save();
        return back()->with( ["success"=>"تغییر نوع طبقه بندی از فرعی به اصلی برای ".$goods_kind_classification->caption." انجام شد." ]);
        }
}
