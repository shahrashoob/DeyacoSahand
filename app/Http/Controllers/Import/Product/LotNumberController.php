<?php

namespace App\Http\Controllers\Import\Product;

use App\Http\Controllers\Controller;
use App\Imports\Product\BOMImport;
use App\Imports\Product\LotNumberImport;
use App\Models\LineProduct\Import\ImportLotNumber;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\NewBOM;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class LotNumberController extends Controller {
    public function index() {

        $model = [ "name" => "lot_numbers", "route" => "import.product.lot_number.upload", "caption" => " همبافت " ];

        return view( "import/index", compact( "model" ) );

    }

    public function upload() {

        Excel::import( new LotNumberImport(), request()->file( 'file_uploaded' ) );

        return redirect()->route( "import.product.lot_number.show" );
    }

    public function show() {

        $list = ImportLotNumber::orderBy( "error", "desc" )->paginate( 200 );

        $error_count = ImportLotNumber::where( "error", "!=", "" )->count();

        $count = ImportLotNumber::count();

        return view( "import.product.lot_number_list", compact( "list", "error_count", "count" ) );
    }

    public function update() {


        $list = ImportLotNumber::select( "product_id", "code", "nosa_code" )->get()->toArray();

        foreach ( $list as $item ) {
            LotNumber::firstOrCreate($item);
        }
        ImportLotNumber::where( "id", ">", 0 )->delete();

        return redirect()->route( "import.product.lot_number.index" )->with( [ "success" => "آپلود با موفقیت انجام شده" ] );


    }
}
