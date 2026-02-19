<?php

namespace App\Http\Controllers\Import\Product;

use App\Exports\Product\ProductPropertySampleFormatExport;
use App\Http\Controllers\Controller;
use App\Imports\Product\ProductPropertyImport;
use App\Imports\Product\ProductPurchaseImport;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Import\ImportProductProperty;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class PropertyController extends Controller {
    var $model = [
        "name"    => "product_property",
        "route"   => "import.product.property.upload",
        "caption" => " اطلاعات مشخصات کالا "
    ];
    var $route_path = "import.product.property.";

    public function index() {
        $model = $this->model;

        return view( "import/product/property/index", compact( "model" ) );

    }

    public function upload() {

        Excel::import( new ProductPropertyImport(), request()->file( 'file_uploaded' ) );

        return redirect()->route( $this->route_path . "show" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );

    }

    public function show() {

        $list = ImportProductProperty::orderBy( "error", "desc" )->paginate( 50 );

        $error_count = ImportProductProperty::where( "error", "!=", "" )->count();

        return view( "import.product.property.show", compact( "list", "error_count" ) );

    }

    public function update() {

        $row  = 0;
        $list = ImportProductProperty::where( "error", "" )->get();
        foreach ( $list as $item ) {
            $row = $row == 0 ? $item->id : $row;
            if($item->id - $row > 200){
                $count=ImportProductProperty::where( "error", "" )->count();
                return view("import.product.property.continue",["count"=>$count]);
            }
            GoodsKindPropertyValue::where( [
                "product_id"             => $item->product_id,
                "goods_kind_property_id" => $item->property_id
            ] )->delete();

            GoodsKindPropertyValue::create( [
                "product_id"             => $item->product_id,
                "goods_kind_property_id" => $item->property_id,
                "value"                  => $item->value
            ] );
            ImportProductProperty::where( [
                "id"             => $item->id
            ] )->delete();
        }

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );
    }

    public function download_page() {
        $goods_kind_option = Option::get( "goods_kind" );
        $model             = $this->model;

        return view( "import/product/property/download_page", compact( "model", "goods_kind_option" ) );
    }

    public     function download_excel(         Request $request) {

        $goods_kind            = GoodsKind::find( $request->goods_kind_id );
        $export                = new ProductPropertySampleFormatExport();
        $export->goods_kind_id = $request->goods_kind_id;

        return Excel::download( $export, 'product_property.' . $goods_kind->caption_en . '.xlsx' );

    }
}

