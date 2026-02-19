<?php

namespace App\Http\Controllers\Warehouse\Pallet;

use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product\BOM\BOMFaultIllegal;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormSessionData;
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

class AddPackingFormController extends Controller
{

    public static $view_path = "warehouse.pallet.add_packing_form.";
    public static $message_type_id = 400; // افزودن به پالت

    public function __construct()
    {
    }

    public function index(Pallet $pallet, Request $request)
    {

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $pallet->id, self::$message_type_id, ['packing_form_read_ids'=>[]]);

        $packing_form_read_ids = [

        ];

        $data["packing_form_read_ids"] = [-1];
        ProductRequestFormSessionData::setDataByOtherId($user_id, $pallet->id, self::$message_type_id, $data);
        if ($pallet->items()->count() == 0) {
            return back()->withErrors("با توجه به اینکه این پالت کاملا خالی شده است، امکان اضافه کردن بسته بندی به آن وجود ندارد.");
        }

        // باتوجه به انبار اولین بسته بندی تشخیص می دهیم که پین بگیریم یا کد بسته بندی
        $allow_entry_with_pin = $pallet->items()->first()->packing_form->warehouse->allow_entry_with_pin ?? 1;

        $packing_list_json_data = PackingForm::
        whereIn("status_id", [7007002, 7007003, 7007005, 7007015])->
        selectRaw("packing_forms.id,packing_forms.code,packing_type_id,pin1")->
        get()->keyBy(
            $allow_entry_with_pin ? "pin1" : "code"
        );

        return view(self::$view_path . "index", compact("packing_form_read_ids", "allow_entry_with_pin", "packing_list_json_data", "pallet", "user_id"));

    }


    public function show_list(Pallet $pallet)
    {

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $pallet->id, self::$message_type_id);


        $packing_form_read_ids = json_decode($data["packing_form_read_ids"]);


        $packing_forms = PackingForm::whereIn("id", $packing_form_read_ids)->get();


        $repetitive_packing_forms = PalletItem::where("pallet_id", $pallet->id)->whereIn("packing_form_id", $packing_form_read_ids)->pluck("packing_form_id")->toArray();

        return view(self::$view_path . "show_list", compact("pallet", "packing_forms", "packing_form_read_ids", "repetitive_packing_forms"));

    }

    public function confirm(Pallet $pallet, Request $request)
    {

        $user_id = Auth::id();
        $data = ProductRequestFormSessionData::getDataByOtherId($user_id, $pallet->id, self::$message_type_id);


        $packing_form_read_ids = json_decode($data["packing_form_read_ids"]);


        $packing_forms = PackingForm::whereIn("id", $packing_form_read_ids)->get();

        foreach ($packing_forms as $packing_form) {
            Pallet::AddPackingForm($pallet, $packing_form, true);
        }

        return redirect()->route("wh.pallet.dashboard.index")->with(["success" => "افزودن بسته بندی های به پالت با موفقیت انجام شد."]);
    }

    public function add_remove_packing_form_pallet_api(Request $request)
    {
        $packing_form_ids = json_encode($request->packing_form_read_ids, true);
        // return $request->user_id."--". $request->pallet_id."****". $request->message_type_id;
        $data = ProductRequestFormSessionData::getDataByOtherId($request->user_id, $request->pallet_id, $request->message_type_id);
        $data["packing_form_read_ids"] = $packing_form_ids;
        ProductRequestFormSessionData::setDataByOtherId($request->user_id, $request->pallet_id, $request->message_type_id, $data);
        return $packing_form_ids;
    }
}