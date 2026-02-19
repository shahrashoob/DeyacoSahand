<?php

namespace App\Http\Controllers\Warehouse\Input;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1008Controller;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Order\OppKind;
use App\Models\Order\TransKind;
use App\Models\Utility\Option;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ComplatedInformationwithValueController extends Controller
{
    //
    public $route_path = "wh.wh.dashboard.index.";
    public $view_path = "warehouse.input.complated_information_with_value.";

    public function  index()
    {
        return view($this->view_path."index" );
    }


    public  function data_to_view_api(Request $request)
    {

        $data = $request->input('data');


           return view($this->view_path . "_view", compact(
               'data'
           ));


    }
}

