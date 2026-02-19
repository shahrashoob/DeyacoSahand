<?php

namespace App\Http\Controllers\LineProductStation\Product\BOM;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorOperation;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\MachineTypeInputBand;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMDegree;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\BOM\BOMPermutation;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Utility\Option;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;

class BOMLogController extends Controller
{
    var $view_path = "line_product_station.product.bom.bom_log.";
    var $route_path = "line_product_station.product.bom.bom_log.";

    public function index(BOM $bom, $product_creation_process = null)
    {

       $bom_logs= Product\BOM\BOMLog::where("bill_of_material_id",$bom->id)->with("items")->paginate(10);
$product=$bom->product;
$view_path = $this->view_path;
        return view($this->view_path . "index", compact("bom_logs","bom", "product_creation_process","product","view_path"));

    }

}
