<?php

namespace App\Http\Controllers\Purchase;

use App\Http\Controllers\Controller;
use App\Models\Purchase\PurchaseOrder;
use App\Models\Purchase\PurchaseOrderProduct;
use Illuminate\Http\Request;

class PurchaseController extends Controller
{
    //
    public function list(){

        $list=PurchaseOrderProduct::paginate();
      
        return view("purchase.purchase_order.purchase_product_list",compact("list"));
    }
    public function view_details(PurchaseOrderProduct $purchaseOrderProduct){

        return view("purchase.purchase_order.view_details",compact("purchaseOrderProduct"));
       

    }
}
