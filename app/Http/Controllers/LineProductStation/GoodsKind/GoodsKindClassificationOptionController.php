<?php

namespace App\Http\Controllers\LineProductStation\GoodsKind;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationOption;
use App\Models\LineProduct\GoodsKind\GoodsKindClassificationProduct;
use Illuminate\Http\Request;

class GoodsKindClassificationOptionController extends Controller {
    private $view_path = "line_product_station.goods_kind.classification.option.";
    private $route_path = "line_product_station.goods_kind.classification.option.";

    public function index( GoodsKindClassification $goods_kind_classification ) {

        return view( $this->view_path . "index", compact( "goods_kind_classification" ) );
    }

    public function create( GoodsKindClassification $goods_kind_classification ) {

        return view( $this->view_path . "create", compact( "goods_kind_classification" ) );
    }

    public function store( Request $request, GoodsKindClassification $goods_kind_classification ) {

        if ( $request->caption == "" || GoodsKindClassificationOption::ExistsCaption( $request->caption, $goods_kind_classification ) ) {
            return back()->withErrors( "عنوان تکراری است" );
        }


        $request["goods_kind_classification_id"] = $goods_kind_classification->id;
        GoodsKindClassificationOption::create( $request->all() );

        return redirect()->route( $this->route_path . "index", $goods_kind_classification )->with( [ "success" => "طبقه با موفقیت اضافه شد" ] );

    }

    public function edit( GoodsKindClassificationOption $goods_kind_classification_option ) {


        return view( $this->view_path . "edit", compact( "goods_kind_classification_option" ) );

    }

    public function update( Request $request, GoodsKindClassificationOption $goods_kind_classification_option ) {

        if ( $request->caption == "" || GoodsKindClassificationOption::ExistsCaption(
                $request->caption,
                $goods_kind_classification_option->goods_kind_classification,
                $goods_kind_classification_option->id
            )
        ) {
            return back()->withErrors( "عنوان تکراری است" );
        }
        $goods_kind_classification_option->update( $request->all() );

        return redirect()->route( $this->route_path . "index", $goods_kind_classification_option->goods_kind_classification )->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }

    public function destroy( GoodsKindClassificationOption $goods_kind_classification_option ) {

        if (
            GoodsKindClassificationProduct::where( "goods_kind_classification_option_id", $goods_kind_classification_option->id )->exists()
        ) {
            return back()->withErrors( "به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد" );
        }
        $goods_kind_classification = $goods_kind_classification_option->goods_kind_classification;
        $goods_kind_classification_option->delete();

        return redirect()->route( $this->route_path . "index", $goods_kind_classification )->with( [ "success" => "یک آیتم با موفقیت حذف گردید" ] );

    }

}
