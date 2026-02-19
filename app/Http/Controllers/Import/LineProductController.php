<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\LineProduct;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LineProductImport;

class LineProductController extends Controller {
    //
    var  $route_path="import.line_product.";
    public function index() {

        $step  = 4;
        $model = [ "name"    => "line_product",
                   "route"   => "import.line_product.upload",
                   "caption" => " لیست خط - محصول - ایستگاه "
        ];

        return view( "import/index", compact( "model", "step" ) );

    }

    public function upload() {

        Excel::import( new LineProductImport, request()->file( 'file_uploaded' ) );

        return redirect()->route( "import.line_product.show" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );

    }

    public function show() {
        $list        = LineProduct\Import\LineProductStation::orderBy( "error", "desc" )->paginate( 50 );
        $error_count = LineProduct\Import\LineProductStation::where( "error", "!=", "" )->count();

        return view( "import/line_product_station", compact( "list", "error_count" ) );
    }

    public function update() {

        $product_ids = LineProduct\Import\LineProductStation::where( "error", "" )->groupBy( "product_id" )->pluck( "product_id" );

        LineProduct\LineProductStation::whereIn( "product_id", $product_ids )->delete();


        $list = LineProduct\Import\LineProductStation::where( "error", "" )->get();
        foreach ( $list as $item ) {

            $new_line_product              = $item->toArray();

            $new_line_product["status_id"] = 1200;
            $line_product                  = LineProduct\LineProductStation::create( $new_line_product );

        }
        return redirect( )->route($this->route_path."index")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
}
