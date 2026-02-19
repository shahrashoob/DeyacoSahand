<?php

namespace App\Http\Controllers\Utility\Help;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIController extends Controller {
    //
    public $view_path = "utility.help.api.";

    public function index() {
        return view( $this->view_path . "index" );
    }

    public function fabric_raw_quality_control() {
        $output_format = [
            "result" => [
                "status"  => "404",
                "message" => "متد تعریف نشده است"
            ],
            "data"=>[
                "form_code"=>100,
                "device_code"=>1,
            ]
        ];

        return view( $this->view_path . "fabric_raw_quality_control" );
    }

}
