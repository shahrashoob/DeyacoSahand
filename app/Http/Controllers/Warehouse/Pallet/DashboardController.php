<?php

namespace App\Http\Controllers\Warehouse\Pallet;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Printer\PrinterFiles;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Warehouse\Pallet\Pallet;
use App\Models\Warehouse\Pallet\PalletItem;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Psy\Util\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class DashboardController extends Controller
{

    public static $view_path = "warehouse.pallet.dashboard.";


    public function __construct()
    {
    }

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $status_id = $request->status_id;
            $order_by = $request->order_by;
        } else {
            $search = session("search_warehouse_input");
            $status_id = session("warehouse_input_status_id");
            $order_by = session("warehouse_input_order_by") ?? "created_at__desc";

        }
        session([
            "search_warehouse_input" => $search,
            "warehouse_input_status_id" => $status_id,
            "warehouse_input_order_by" => $order_by,
        ]);


        $status_option = Option::get("status", $status_id, 6080);


        $list = Pallet::join("status", "status.id", "status_id")->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("pallets.code", "like", "%" . $search . "%");
            });

        })->
        select("pallets.*")->with("status")->
        orderBy("pallets.id", "desc")->
        paginate();

        return view(self::$view_path . "index", compact("status_option", "list", "search", "order_by"));

    }

    public function view(Pallet $pallet)
    {
       $product_list= $products=PalletItem::
        join("packing_form_item","packing_form_item.packing_form_id","pallet_items.packing_form_id")->
            join("products","products.id","product_id")->
            where("pallet_id",$pallet->id)->
            groupBy("product_id")->
            selectRaw("products.code,products.caption")->
            get();


        return view(self::$view_path . "view", compact("pallet","product_list"));

    }

    public function download(Pallet $pallet, PackingTypeLabelPrintingType $packingTypeLabelPrintingType)
    {
        $controller = new PrintPalletController();
        return $controller->download($pallet, $packingTypeLabelPrintingType->id);
    }

    public function print(Pallet $pallet, PackingTypeLabelPrintingType $packingTypeLabelPrintingType)
    {
        $worker = Worker::find(Auth::id());
        PrintPalletController::direct_print($pallet, $worker, $packingTypeLabelPrintingType->id);
        return back()->with(["success" => "پرینت با موفقیت برای چاپ ارسال گردید."]);
    }
}