<?php

namespace App\Http\Controllers\Import\Product;

use App\Http\Controllers\Controller;
use App\Imports\Product\BOMImport;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\NewBOM;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BOMController extends Controller {

    public function index() {
        $model = [ "name" => "bom", "route" => "import.product.bom.upload", "caption" => " BOM " ];

        return view( "import/index", compact( "model" ) );

    }

    public function upload() {

        Excel::import( new BOMImport, request()->file( 'file_uploaded' ) );

        return redirect()->route( "import.product.bom.show" );
    }

    public function show() {

        $list = NewBOM::orderBy( "error", "desc" )->paginate( 200 );

        $error_count = NewBOM::where( "error", "!=", "" )->count();

        $count = NewBOM::count();

        return view( "import.product.bom_list", compact( "list", "error_count", "count" ) );
    }

    public function update() {

        $product_ids = NewBOM::groupBy( "product_id" )->pluck( "product_id" );
        BOM::whereIn( "product_id", $product_ids )->delete();
        BOMDegree::whereIn( "product_id", $product_ids )->delete();

        $list = NewBOM::all();
        foreach ( $list as $new_bom ) {
            BOM::whereIn( "product_id", $new_bom )->delete();
            $bom = BOM::create( $new_bom->toArray() );

            foreach ( $new_bom->degrees as $degree_item ) {
                $new_degree=$degree_item->toArray();
                $new_degree["bill_of_material_id"]=$bom->id;
                BOMDegree::create( $new_degree);

            }
        }


        NewBOM::where( "id", ">", 0 )->delete();

        return redirect()->route( "import.product.bom.index" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );


    }
}
