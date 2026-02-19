<?php

namespace App\Http\Controllers\Warehouse\WarehouseShelving;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Reservoir\Reservoir;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Warehouse\Shelving\ShelvingCell;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelving;
use App\Models\Warehouse\WarehouseShelving\WarehouseShelvingProduct;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Milon\Barcode\DNS1D;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use function back;
use function redirect;
use function session;
use function view;

class DashboardController extends Controller
{
    //
    private $view_path = "warehouse.warehouse_shelving.dashboard.";
    private $route_path = "wh.warehouse_shelving.dashboard.";

    public function view_qr(WarehouseShelving $warehouseShelving)
    {

        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
            if (!$post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.view_qr")) {
            return back()->withErrors("دسترسی به صفحه مورد نظر برای شما وجود ندارد.");
        }

        $allowed_warehouse_ids = Warehouse::getAllowedWarehouse();
        if (!in_array($warehouseShelving->warehouse_id, $allowed_warehouse_ids)) {
            return back()->withErrors("امکان مشاهده اطلاعات  " . $warehouseShelving->warehouse->caption . " برای شما مقدور نمی باشد.");
        }
        // وضعیت امکان افزودن بسته بندی
        $add_add_packing = $post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.add_packing");


        $warehouse_shelving_product_list =
            WarehouseShelvingProduct::
            where("warehouse_shelving_id", $warehouseShelving->id)->
            orWhere("warehouse_shelving_type_id", $warehouseShelving->warehouse_shelving_type_id)->
            with("product")->paginate(20);

        $packing_forms = PackingForm::where("warehouse_shelving_id", $warehouseShelving->id)->paginate(20);

        $reservoirs = Reservoir::where("warehouse_shelving_id", $warehouseShelving->id)->paginate(20);

        return view($this->view_path . "index", compact("warehouseShelving", "warehouse_shelving_product_list", "packing_forms", "add_add_packing", "reservoirs"));
    }

    public function DCWS_ShortLink($warehouseShelving_id)
    {
        $warehouseShelving = WarehouseShelving::find($warehouseShelving_id);
        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if (!$post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.view_qr")) {
            return back()->withErrors("دسترسی به صفحه مورد نظر برای شما وجود ندارد.");
        }
        $warehouseShelving = WarehouseShelving::find($warehouseShelving_id);
        if (!$warehouseShelving) {
            return back()->withErrors("دسترسی به صفحه مورد نظر امکان پذیر نمی باشد.");
        }

        return $this->view_qr($warehouseShelving);
    }

    public function submit_add_packing_form(Request $request, WarehouseShelving $warehouseShelving)
    {

        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if (!$post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.add_packing")) {
            return back()->withErrors("دسترسی به صفحه مورد نظر برای شما وجود ندارد.");
        }


        if (!$warehouseShelving->can_product_directly_in_location) {
            return back()->withErrors("امکان ورود کالا به صورت مستقیم به این محل از انبار امکان پذیر نمی باشد.");
        }

        if ($warehouseShelving->warehouse->allow_entry_with_pin) {
            $packing_form = PackingForm::where("pin1", $request->packing_form_pin)->first();
        } else {
            $packing_form = PackingForm::where("code", "DCPK/" . $request->packing_form_code)->first();
        }

        if (!$packing_form) {
            return back()->withErrors("کد بسته بندی یافت نشد");
        }

        $product_ids = $packing_form->items->pluck('product_id')->toArray();
        $message = "";
        $product_have_specific_location=Product::whereIn("id", $product_ids)->pluck("product_have_specific_location","id")->toArray();
        foreach ($product_ids as $product_id) {
            $result = WarehouseShelvingProduct::CheckForProduct($warehouseShelving, $product_id,$product_have_specific_location[$product_id]);
            if (!$result["result"]) {
                $message .= $result["error"];
            }
        }

        if ($message != "") {
            return back()->withErrors($message);
        }
        if ($packing_form->warehouse_shelving_id == $warehouseShelving->id) {
            return back()->withErrors("این بسته بندی قبلا در این محل قرار گرفته است");
        }
        $packing_form->warehouse_shelving_id = $warehouseShelving->id;
        $packing_form->save();

        return redirect()->route($this->route_path . "view_qr", $warehouseShelving)->with(["success" => "اطلاعات با موفقیت ثبت گردید."]);

    }

    public function submit_add_reservoir(Request $request, WarehouseShelving $warehouseShelving)
    {

        $post_user = \Auth::user()->posts->first();
        // اگر وضعیت نامعتیر است یا مجوز ندارد، خطا بدهد.
        if (!$post_user->checkButtonPermission("wh.warehouse_shelving.dashboard.add_packing")) {
            return back()->withErrors("دسترسی به صفحه مورد نظر برای شما وجود ندارد.");
        }

        if (!$warehouseShelving->can_product_directly_in_location) {
            return back()->withErrors("امکان ورود کالا به صورت مستقیم به این محل از انبار امکان پذیر نمی باشد.");
        }

        $packing_form_reservoir = PackingForm::where("code", "DCRC/" . $request->reservoir_code)->first();

        if (!$packing_form_reservoir) {
            return back()->withErrors("کد مخزن یافت نشد");
        }

        $reservoir = Reservoir::where("packing_form_id", $packing_form_reservoir->id)->first();
        if (!$packing_form_reservoir) {
            return back()->withErrors("مخزن مرتبط با کد یافت نشد، لطفا با پشیتبانی تماس بگیرید.");
        }
        $product_ids = $reservoir->products->pluck('product_id')->toArray();
        $product_have_specific_location=Product::whereIn("id", $product_ids)->pluck("product_have_specific_location","id")->toArray();
        $message = "";
        foreach ($product_ids as $product_id) {
            $result = WarehouseShelvingProduct::CheckForProduct($warehouseShelving, $product_id,$product_have_specific_location[$product_id]);
            if (!$result["result"]) {
                $message .= $result["error"];
            }
        }

        if ($message != "") {
            return back()->withErrors($message);
        }
        if ($reservoir->warehouse_shelving_id == $warehouseShelving->id) {
            return back()->withErrors("این مخزن قبلا در این محل قرار گرفته است");
        }
        $reservoir->warehouse_shelving_id = $warehouseShelving->id;
        $reservoir->save();

        return redirect()->route($this->route_path . "view_qr", $warehouseShelving)->with(["success" => "اطلاعات با موفقیت ثبت گردید."]);

    }
}