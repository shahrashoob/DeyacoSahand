<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\WorkerImport;

class WorkerController extends Controller
{

    //
    public function index( ) {

        $step=4;
        $model= ["name"=>"hr", "route" => "import.worker.upload", "caption" => "فایل لیست کارکنان " ];
        return view( "import/index", compact( "model","step") );

    }
    public function upload(){

        Excel::import( new WorkerImport, request()->file( 'file_uploaded' ) );

        return redirect( )->route("import.worker.index")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
    public function show()
    {

       $list=[];
       $error_count=0;
        return view("import/line_list",compact("list","error_count"));

    }
}
