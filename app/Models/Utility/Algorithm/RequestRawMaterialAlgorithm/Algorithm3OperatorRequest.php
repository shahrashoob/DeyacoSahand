<?php

namespace App\Models\Utility\Algorithm\RequestRawMaterialAlgorithm;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\Utility\Script\Script;

class Algorithm3OperatorRequest extends Controller {

    //    // داخل این الگوریتم در کلاس زیر پیاده سازی شده است و بعد باید برای یک دست شدن الگوریتم ها همه آنها را یکی کنیم.
    public function empty()
    {
        \App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\Algorithm3OperatorRequest::RequestForMachine();
    }}
