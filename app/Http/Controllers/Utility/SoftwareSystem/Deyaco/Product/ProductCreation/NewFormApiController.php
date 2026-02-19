<?php

namespace App\Http\Controllers\Utility\SoftwareSystem\Deyaco\Product\ProductCreation;

use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\Product\ProductCreation\NewFormController;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\MultipartStream;
use GuzzleHttp\Psr7\Stream;
use Illuminate\Http\Request;


class NewFormApiController extends Controller
{
    public function submit(Request $request)
    {
        return NewFormController::PostSubmitApi($request);
    }

}
