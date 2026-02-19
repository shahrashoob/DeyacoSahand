<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcessPriority;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;


class PrioritySettingController extends Controller
{
    // تنظیمات
    private $view_path = "line_product_station.product.product_creation.priority_setting.";
    private $route_path = "line_product_station.product.product_creation.priority_setting.";

    public function index()
    {
        $values = Setting::getValues();
        return view($this->view_path . "index", compact("values"));
    }

    public function submit(Request $request)
    {
        1 / 0;
    }
}
