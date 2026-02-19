<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\LineImport;

class LineController extends Controller
{
    
    //
    public function index( ) {
        
        $step=4;
        $model= ["name"=>"line", "route" => "import.line.upload", "caption" => "فایل لیست خط های تولید " ];
        return view( "import/index", compact( "model","step") );
              
    }
    public function upload(){

        Excel::import( new LineImport, request()->file( 'file_uploaded' ) );
     
        return redirect( )->route("import.line.index")->with(["success"=>"آپلود با موفقیت انجام شده"]);

    }
    public function show()
    {
        
       $list=[];
       $error_count=0;
        return view("import/line_list",compact("list","error_count"));

    }
}
