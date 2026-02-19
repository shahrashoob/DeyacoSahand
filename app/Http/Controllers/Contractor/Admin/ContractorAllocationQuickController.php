<?php

namespace App\Http\Controllers\Contractor\Admin;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Form\PackingLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\ConsumedProduct\ConsumedProduct;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionLog;
use App\Models\SoftwareSystem\SoftwareSystem;
use App\Models\Utility\Address\Address;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SpecialLicense\SpecialLicense;
use App\Models\Warehouse\WarehouseProduct;
use App\Notifications\SMSNotification;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class ContractorAllocationQuickController extends Controller
{
    public static $info = [
        "route" => "contractor.admin.contractor_allocation_quick.",
        "view" => "contractor.admin.contractor_allocation_quick.",
        "enable_status" => ["001", "002", "004", "006"],
        "button" => ["caption" => "تخصیص پیمانکار", "class" => "btn-primary"],

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "contractor.admin.dashboard.";

    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view"];
    }

    public function index($contractor_id = 0)
    {

        if ($contractor_id == 0) {
            $last_allocation = Allocation::whereNotNull("contractor_id")->orderByDesc("id")->first();
            $contractor_id = $last_allocation->contractor_id ?? 0;
        }
        $contractor_option = Option::get("contractor", $contractor_id);

        $production_channel_type_option = Option::get("contractor_production_channel_type", 0, $contractor_id);
        return view($this->view_path . "index", compact('contractor_option', "production_channel_type_option", "contractor_id"));
    }

    public function submit(Request $request)
    {

        $contractor = Contractor::find($request->contractor_id);
        $production_channel_type = ProductionChannelType::where("caption", "like", "%" . $request->production_channel_type_id_auto . "%")->first();

        if (!$production_channel_type) {
            return back()->withErrors("لطفا کانال تولید را انتخاب نمایید.");
        }
        $jsn_data_list = JsonDataList::firstOrcreate(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ],
            [
                "data" => "[]"
            ]);
        $data = json_decode($jsn_data_list->data, true);
        if (isset($data["contractor_id"])) {
            // اگر یک کانال تولید برای دو پیمانکار تعریف شده باشد، اولین بار با پیمانکار 1 یک فرم سریع ایجاد کردند ، بعد بدون اینکه آن را تکمیل کنند، خواسند برای پیمانکار 2 ایجاد کنند، که با این خطا مواجه می شوند.
            if ($data["contractor_id"] != $request->contractor_id) {
                return back()->withErrors("شما قبلا کانال تولید را برای پیمانکار *** انتخاب نموده اید، لطفا ابتدا آن را کامل نمایید.");
            }
        }

        $data["contractor_id"] = $request->contractor_id;

        $jsn_data_list->data = json_encode($data);
        $jsn_data_list->save();

        return redirect()->route($this->route_path . "select_packing_forms", compact("contractor", "production_channel_type"));

    }

    public function select_packing_forms(Contractor $contractor, ProductionChannelType $production_channel_type)
    {

//        $result = $this->checkPermission($production);
//        if ($result != "") {
//            return $result;
//        }


        $packing_list_data = null;
        $packing_list_transport_item = [];

        $result = ContractorAllocationController::GetSelectPackingFormData($contractor, $production_channel_type, null, true);

        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }
        $packing_list_json_data = $result["packing_list_json_data"];
        $selected_packing_ids = $result["selected_packing_ids"];
        $count_select = $result["count_select"];
        $allow_entry_with_pin = $result["allow_entry_with_pin"];

        return view($this->view_path . "select_packing_forms",
            compact("packing_list_data", "packing_list_transport_item",
                "packing_list_json_data", 'contractor',
                "selected_packing_ids", "count_select", "allow_entry_with_pin", "production_channel_type"
            )
        );
    }

    /*
     *
     * انتخاب یک کارت تولید و رفتن به مرحله بعد
     */
    public function go_to_contractor_allocation(Contractor $contractor, ProductionChannelType $production_channel_type)
    {

        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ])->first();
        $data = json_decode($jsn_data_list->data, true);
        if (isset($data["contractor_id"])) {
            // اگر یک کانال تولید برای دو پیمانکار تعریف شده باشد، اولین بار با پیمانکار 1 یک فرم سریع ایجاد کردند ، بعد بدون اینکه آن را تکمیل کنند، خواسند برای پیمانکار 2 ایجاد کنند، که با این خطا مواجه می شوند.
            if ($data["contractor_id"] != $contractor->id) {
                return back()->withErrors("شما قبلا کانال تولید را برای پیمانکار *** انتخاب نموده اید، لطفا ابتدا آن را کامل نمایید.");
            }
        }

        if (count($data["packing_form_ids"]) == 0) {
            return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }


        $packing_form_items = PackingFormItem::whereIn("packing_form_id", $data["packing_form_ids"])->get();
        $parent_production_terminate_packing_forms = []; // بسته بندی هایی که کارت سطح بالای آنها خاتمه یافته شده
        $parent_production_null_packing_forms = []; // بسته بندی هایی که کارت سطح بالا ندارند
        $parent_production_where_product_is_diff = []; // بسته بندی هایی که کانال تولید کارت سطح بالای آن با این کارت متفاوت است.
        $parent_production_id_is_ok = false;
        $parent_production_id_is_null_ok = false;

        $special_licence_packing_forms = [];
        foreach ($packing_form_items as $packing_form_item) {
            $has_error = false;
            $parent_production_id = $packing_form_item->production_form_item->production->parent_production_id ?? null;
            if (!$parent_production_id) {
                $special_licence = self::HasSpecialLicense($packing_form_item->packing_form_id, $production_channel_type);
                if (!$special_licence) {
                    $parent_production_null_packing_forms[] = $packing_form_item;

                } else {

                    $production_ss = Production::find($special_licence->param2);
                    if (!$production_ss) {
                        return redirect()->route($this->route_path . "show_selected_packing", [$contractor, $production_channel_type])->withErrors("مجوز مربوط به بسته بندی *** نامعتبر می باشد، کارت پیمان مربوطه یافت نشد.");
                    }

                    $special_licence_packing_forms[$packing_form_item->packing_form_id] = [
                        "production_id" => $production_ss->id,
                        "product_id" => $production_ss->product_id,
                    ];
                    $parent_production_id_is_null_ok = $production_ss;
                }

            } else {
                $parent_production = $packing_form_item->production_form_item->production->parent_production;

                $parent_production_product_line_product = LineProductStation::where("product_id", $parent_production->product_id)->first();

                if (!$parent_production_product_line_product) {
                    return back()->withErrors(" کانال تولید کالای " . $parent_production->product->caption . " قابل تشخیص نمی باشد، لطفا اطلاعات مسیر محصول کالا را بررسی کنید.");
                }
                // کانال تولید های سطح بالا فرق دارد
                if ($parent_production_product_line_product->production_channel_type_id != $production_channel_type->id) {
                    $special_licence = self::HasSpecialLicense($packing_form_item->packing_form_id, $production_channel_type);
                    if (!$special_licence) {
                        $parent_production_where_product_is_diff[] = $packing_form_item;
                    } else {

                        $production_ss = Production::find($special_licence->param2);
                        if (!$production_ss) {
                            return redirect()->route($this->route_path . "show_selected_packing", [$contractor, $production_channel_type])->withErrors("مجوز مربوط به بسته بندی *** نامعتبر می باشد، کارت پیمان مربوطه یافت نشد.");
                        }
                        if ($production_ss->waiting_status_id == 7008005) { // خاتمه یافته)
                            return back()->withErrors("کارت پیمان مجوز بسته بندی " . $packing_form_item->packing_form->code . " خاتمه یافته شده است و امکان ثبت تخصیص برای آن وجود ندارد.");
                        }
                        $special_licence_packing_forms[$packing_form_item->packing_form_id] = [
                            "production_id" => $production_ss->id,
                            "product_id" => $production_ss->product_id,
                        ];
                        $parent_production_id_is_ok = $production_ss->id;
                    }
                } // کانال تولید سطح بالا خاتمه یافته است.
                elseif ($parent_production->waiting_status_id == 7008005) { // خاتمه یافته

                    $special_licence = self::HasSpecialLicense($packing_form_item->packing_form_id, $production_channel_type);
                    if (!$special_licence) {
                        $parent_production_terminate_packing_forms[] = $packing_form_item;
                    } else {

                        $production_ss = Production::find($special_licence->param2);
                        if (!$production_ss) {
                            return redirect()->route($this->route_path . "show_selected_packing", [$contractor, $production_channel_type])->withErrors("مجوز مربوط به بسته بندی *** نامعتبر می باشد، کارت پیمان مربوطه یافت نشد.");
                        }

                        $special_licence_packing_forms[$packing_form_item->packing_form_id] = [
                            "production_id" => $production_ss->id,
                            "product_id" => $production_ss->product_id,
                        ];

                    }
                } else {
                    $parent_production_id_is_ok = $parent_production->id;
                }
            }
        }

        // ذخیره کارت های پیمان جدید بسته بندی ها
        $data["special_licence_packing_forms"] = $special_licence_packing_forms;

        $jsn_data_list->data = json_encode($data);
        $jsn_data_list->save();

        $row_errors = 0;
        $message_all = "";
        if (count($parent_production_null_packing_forms) > 0) {
            $message = "<br/>";
            foreach ($parent_production_null_packing_forms as $pp_item) {
                $message .= ++$row_errors . "- " . "بسته بندی " . $pp_item->packing_form->code . " " .
                    SpecialLicense::GetLink(
                        17,
                        $pp_item->packing_form_id, "ثبت درخواست مجوز ",
                        $pp_item->production_form_item->production_id ?? 0,
                        0,
                        $contractor->id,
                        $production_channel_type->id
                    )
                    . "<br/>";
            }

            $message =
                "با توجه به اینکه دستور پیمان برای کارت تولید زیر مشخص نشده است، امکان تخصیص به صورت اتومات برای بسته بندی امکان پذیر نمی باشد و می بایست به صورت عادی تخصصی دهید و یا مجوز ثبت نمایید. " .
                $message;
            $message_all .= $message;

        }


        if (count($parent_production_terminate_packing_forms) > 0) {
            $message = "<br/>";
            foreach ($parent_production_terminate_packing_forms as $pp_item) {
                $message .= ++$row_errors . "- " . "بسته بندی " . $pp_item->packing_form->code . " دارای کارت پیمان " . $pp_item->production_form_item->production->parent_production->serial . " " .
                    SpecialLicense::GetLink(
                        17,
                        $pp_item->packing_form_id, "ثبت درخواست مجوز",
                        $pp_item->production_form_item->production_id,
                        0,
                        $contractor->id,
                        $production_channel_type->id
                    )
                    . "<br/>";
            }
            $message_all .=
                "<br/>" .
                " با توجه به اینکه کارت پیمان بسته بندی های زیر خاتمه یافته شده است، امکان ثبت تخصیص اتومات برای بسته بندی ها وجود ندارد." . $message;
        }

        if (count($parent_production_where_product_is_diff) > 0) {
            $message = "<br/>";
            foreach ($parent_production_where_product_is_diff as $pp_item) {
                $message .= ++$row_errors . "- " . "بسته بندی " . $pp_item->packing_form->code . " دارای کارت پیمان " . $pp_item->production_form_item->production->parent_production->serial . " " .
                    SpecialLicense::GetLink(
                        17,
                        $pp_item->packing_form_id, "ثبت درخواست مجوز",
                        $pp_item->production_form_item->production_id,
                        0,
                        $contractor->id,
                        $production_channel_type->id
                    )
                    . "<br/>";
            }

            $message_all .= "<br/>" . "با توجه به اینکه کارت پیمان بسته بندی های زیر با کانال تولید جاری متفاوت هستند، امکان ثبت تخصیص اتومات برای بسته بندی ها وجود ندارد." . $message;
        }

        if ($message_all != "") {
            return redirect()->route($this->route_path . "show_selected_packing", [$contractor, $production_channel_type])->withErrors($message_all);
        }
        if (!$parent_production_id_is_ok) {

            if ($parent_production_id_is_null_ok) {
                $parent_production_id_is_ok = $parent_production_id_is_null_ok;
            } else {
                return redirect()->route($this->route_path . "show_selected_packing", [$contractor, $production_channel_type])->withErrors("وضعیت کارت پیمان همه بسته بندی های انتخاب شده خاتمه یافته می باشد و امکان تخصیص وجود ندارد.");
            }
        }
        return redirect()->route("contractor.admin.contractor_allocation.index", [$parent_production_id_is_ok, $contractor, $production_channel_type->id]);
    }

    public function show_selected_packing(Contractor $contractor, ProductionChannelType $production_channel_type)
    {
        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ])->first();
        $data = json_decode($jsn_data_list->data, true);
        if (isset($data["contractor_id"])) {
            // اگر یک کانال تولید برای دو پیمانکار تعریف شده باشد، اولین بار با پیمانکار 1 یک فرم سریع ایجاد کردند ، بعد بدون اینکه آن را تکمیل کنند، خواسند برای پیمانکار 2 ایجاد کنند، که با این خطا مواجه می شوند.
            if ($data["contractor_id"] != $contractor->id) {
                return back()->withErrors("شما قبلا کانال تولید را برای پیمانکار " . $contractor->fullCaption() . " انتخاب نموده اید، لطفا ابتدا آن را کامل نمایید.");
            }
        }

        if (!isset($data["packing_form_ids"]) || count($data["packing_form_ids"]) == 0) {
            return back()->withErrors("لطفا حداقل یک بسته بندی را انتخاب نمایید.");
        }

        $list = PackingFormItem::whereIn("packing_form_id", $data["packing_form_ids"])->
        with("production_form_item.production.parent_production")->
        paginate();


        return view($this->view_path . "show_selected_packing", compact("list", "contractor", "production_channel_type"));
    }

    public function show_selected_packing_by_packing_form(Contractor $contractor, ProductionChannelType $production_channel_type)
    {
        return redirect()->route($this->route_path . "go_to_contractor_allocation", [$contractor, $production_channel_type]);

    }

    /**
     * حذف بسته بنیدی های انتخاب شده
     **/
    public function remove_packing_forms(Contractor $contractor, ProductionChannelType $production_channel_type, PackingForm $packing_form)
    {


        $jsn_data_list = JsonDataList::where(
            [
                "other_id" => $production_channel_type->id,
                "message_type_id" => 602,
            ])->first();

        $data = json_decode($jsn_data_list->data, true);
        if (!isset($data["packing_form_ids"])) {
            $data["packing_form_ids"] = [];
        }
        $data["packing_form_ids"] = array_filter($data["packing_form_ids"], fn($value) => $value !== $packing_form->id);

        $jsn_data_list->data = json_encode($data);
        $jsn_data_list->save();
        return back()->with(["success" => "بسته بندی با موفقیت حذف گردید."]);
    }


    public
    static function HasSpecialLicense($packing_form_id, $production_channel_type)
    {

        $special_license_list = SpecialLicense::where([
            "special_license_type_id" => 17,
            "reference_id" => $packing_form_id,
            "param4" => $production_channel_type->id,
            "status_id" => 6040002, // تایید شده
        ])->first();

        return $special_license_list;

    }

}