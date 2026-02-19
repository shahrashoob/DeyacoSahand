<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product\ProductRequestPermission\ProductRequestPermission;
use App\Models\Order\Loading\LoadingProcesses;
use App\Models\Order\Order;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class DownloadLicenseController extends Controller
{
    //

    public function index()
    {
      $list=  ProductRequestPermission::orderBy("id", "desc")->paginate(30);

      return view('warehouse.out.download.index', compact('list'));
    }


}
