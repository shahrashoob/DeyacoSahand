<?php

namespace App\Models\Warehouse\WarehouseHandling;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warehouse\WarehouseHandlingEvent;
use App\Http\Controllers\Warehouse\DashboardController;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Production\ProductionFormItem;
use App\Models\User;
use App\Models\Utility\SmartObject;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProductBlock;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class WarehouseHandling extends Model
{
    use HasFactory;

    protected $table = "warehouse_handling";
    protected $fillable = [
        "warehouse_id",
        "user_id",
        "status_id",
        "start_datetime",
        "end_datetime",
        "check_diff_in_amount",
        "check_diff_in_weight",
        "check_diff_in_sub_packing_form_number",
        "max_diff_allowed",
        "use_of_smart_object"

    ];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function products()
    {
        return $this->hasMany(WarehouseHandlingProduct::class);
    }

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function packing_forms()
    {
        return $this->hasMany(WarehouseHandlingPackingForm::class);
    }

    public function logs()
    {
        return $this->hasMany(WarehouseHandlingLog::class);
    }

    public function start_datetime()
    {
        if (!$this->start_datetime) {
            return null;
        }
        return jdate(Carbon::parse($this->start_datetime)->timestamp)->format('Y/m/d');
    }

    public function end_datetime()
    {
        if (!$this->end_datetime) {
            return null;
        }
        return jdate(Carbon::parse($this->end_datetime)->timestamp)->format('Y/m/d');
    }

    public function smart_object()
    {
        return $this->belongsTo(SmartObject::class);
    }

    public function getCode()
    {
        return $this->id;
    }

    /**
     * بسته بندی هایی که خوانده نشده است را حذف می کند.
     * @return void
     */
    public static function RemoveUnReadPackingForm(WarehouseHandling $warehouseHandling)
    {
        // انهایی که خوانده نشده است را حذف می کنیم.
        $warehouseHandling->packing_forms()->
        whereIn("status_id", [524000403, 524000407])-> // خوانده نشده ها را حذف می کنیم.
        delete();
    }

    public static function InitProcess($warehouse_handling_id, $user_id)
    {
        $warehouse_handling = WarehouseHandling::find($warehouse_handling_id);
        if (!$warehouse_handling) {
            return [
                "result" => false,
                "warehouse_handling" => $warehouse_handling,
                "error" => "شماره انبارگردانی به درستی ثبت نشده است."
            ];
        }
        if ($warehouse_handling->status_id != 524000303) {
            return [
                "result" => false,
                "warehouse_handling" => $warehouse_handling,
                "error" => "وضعیت انبارگردانی شماره " . $warehouse_handling_id . " جهت پردازش اولیه نامعتبر است."
            ];
        }

        // حذف بسته بندی هایی که خوانده نشده اند.
        self::RemoveUnReadPackingForm($warehouse_handling);

        // دوباره انبارگردانی را می گیریم که مطئمن شویم بسته بندی ها حذف شده اند.
        $warehouse_handling = WarehouseHandling::find($warehouse_handling_id);

        $warehouse_handling_product_ids = $warehouse_handling->products()->pluck("product_id")->toArray();
        $packing_list_in_warehouse = PackingForm::
        // اگر کالای مجاز داریم، فقط بسته بندی هایی که کالای مجاز در آنها قرار دارد را انتخاب می کنیم.
        when(count($warehouse_handling_product_ids) > 0, function ($query) use ($warehouse_handling_product_ids) {
            return $query->join("packing_form_item", "packing_form_id", "packing_forms.id")->
            whereIn("product_id", $warehouse_handling_product_ids);
        })->
        where("warehouse_id", $warehouse_handling->warehouse_id)->
        whereNull("packing_form_master_id")->
        groupBy("packing_forms.id")->
        select("packing_forms.*")->
        get();

        $packing_list_warehouse_handling = $warehouse_handling->packing_forms()->get()->keyBy("packing_form_id");

        // همه بسته بندی هایی که داخل انبار است و خوانده نشده را بررسی می کنیم.
        foreach ($packing_list_in_warehouse as $packing_form) {
            if (!isset($packing_list_warehouse_handling[$packing_form->id])) {
                $status_id = null;
                // اگر در انبار گردانی وجود ندارد
                if ($packing_form->warehouse_status_id == 4201) {
                    if ($packing_form->status_id == 7007003) {
                        $status_id = 524000403; // خوانده نشده، داخل انبار
                    } else {
                        $status_id = 524000407; // خوانده نشده، وضعیت نامعتبر
                    }
                } else {
                    $status_id = 524000407; // خوانده نشده، وضعیت نامعتبر
                }

                WarehouseHandlingPackingForm::create([
                    "warehouse_handling_id" => $warehouse_handling->id,
                    "packing_form_id" => $packing_form->id,
                    "status_id" => $status_id
                ]);
            } else {
                // در انبار گردانی وجود دارد.

            }
        }

        // تغییر وضعیت انبار گردانی
        if (self::HasErrorInPackingForm($warehouse_handling)) {
            $warehouse_handling->status_id = 524000304; // در انتظار بررسی بسته بندی ها
        } else {
            $warehouse_handling->status_id = 524000311; // در انتظار تایید مرحله اول
        }
        $warehouse_handling->save();
        // پردازش اولیه
        event(new WarehouseHandlingEvent($warehouse_handling, 524000303, "", $user_id));

        return [
            "result" => true
        ];
    }

    public static function EndProcess($warehouse_handling_id, $user_id)
    {

        $warehouse_handling = WarehouseHandling::find($warehouse_handling_id);
        if (!$warehouse_handling) {
            $message = "شماره انبارگردانی به درستی ثبت نشده است.";
            event(new WarehouseHandlingEvent($warehouse_handling, 524000307, $message, $user_id));

            return [
                "result" => true,
                "error" => $message
            ];
        }
        if ($warehouse_handling->status_id != 524000321) {
            $message = "وضعیت انبارگردانی شماره " . $warehouse_handling_id . " جهت پردازش اولیه نامعتبر است.";
            event(new WarehouseHandlingEvent($warehouse_handling, 524000307, $message, $user_id));

            return [
                "result" => true,
                "error" => $message
            ];
        }

        $packing_list_warehouse_handling = $warehouse_handling->packing_forms()->get()->keyBy("packing_form_id");

        $message = "";
        $error_input_to_warehouse = "";
        foreach ($packing_list_warehouse_handling as $item) {
            switch ($item->status_id) {
                case 524000401: //خوانده شده داخل انبار
                    if (
                        $item->packing_form->warehouse_status_id != 4201
                        || $item->packing_form->status_id != 7007003
                        || $item->packing_form->warehouse_id != $warehouse_handling->warehouse_id
                    ) {
                        $message .= "وضعیت بسته بندی " . $item->packing_form->code . " نامعتبر است (کد 1: بسته بندی های خوانده شده (داخل انبار))" . "<br/>";
                    }
                    break;
                case 524000402: //خوانده شده خارج از انبار
                    if (
                        $item->packing_form->warehouse_status_id != 4202
                        ||
                        $item->packing_form->status_id == 7007003
                    ) {
                        $message .= "وضعیت بسته بندی " . $item->packing_form->code . " نامعتبر است (کد 2: خوانده شده - خارج از انبار)" . "<br/>";
                    }
                    break;
                case 524000403: //خوانده نشده (داخل انبار)
                    if (
                        $item->packing_form->warehouse_status_id != 4201
                        ||
                        $item->packing_form->status_id != 7007003
                    ) {
                        $message .= "وضعیت بسته بندی " . $item->packing_form->code . " نامعتبر است (کد 3- خوانده نشده (داخل انبار))" . "<br/>";
                    }
                    break;
                case 524000408: //خوانده شده (در انتظار تایید انبار)
                    if (
                        $item->packing_form->warehouse_status_id != 4204
                        ||
                        $item->packing_form->status_id != 7007002
                    ) {
                        $message .= "وضعیت بسته بندی " . $item->packing_form->code . " نامعتبر است (کد 4- خوانده شده (در انتظار تایید انبار))" . "<br/>";
                    } else {
                        // ابتدا لیست بسته بندی هایی که در انتظار تایید انبار هستند را تایید می کنیم.
                        $result_input_to_warehouse = DashboardController::SubmitNewPackingToWarehouse($item->packing_form, $user_id,false);
                        if (!$result_input_to_warehouse["result"]) {
                            $error_input_to_warehouse .= "<br/>". $item->packing_form->code ."=>". $result_input_to_warehouse["error"];
                        }

                    }
                    break;
                default:
                    $message .= "وضعیت بسته بندی " . $item->packing_form->code . " نامعتبر است (کد 4)" . "<br/>";
                    break;
            }
        }

        // خطای ورود به انبار
        if ($error_input_to_warehouse != "") {
            $message .= "<br/>" . "ماژول وورد به انبار در زمان تایید بسته بندی ها می گوید:" . $error_input_to_warehouse;;
        }
        if ($message != "") {
            $warehouse_handling->status_id = 524000304; // در انتظار بررسی بسته بندی ها
            $warehouse_handling->save();
            // پردازش نهایی
            event(new WarehouseHandlingEvent($warehouse_handling, 524000307, $message, $user_id));
            return [
                "result" => true
            ];
        }


        // اطلاعات بسته بندی های معتبر است و باید فرم های انبار را ثبت و تایید کنیم.

        $input_form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "production_card_id" => 0,
            "user_id" => $user_id,
            "form_type_id" => 304,
            "trans_kind" => 38, // کسری انبار گردانی
            "warehouse_id" => $warehouse_handling->warehouse_id,
            "status_id" => 500000200, // تایید شده
            "ic" => $warehouse_handling->warehouse->ic ?? (1 / 0)
        ]);
        $input_form->getCode("DCRF");
        $input_form->getRandom();

        $output_form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "production_card_id" => 0,
            "user_id" => $user_id,
            "form_type_id" => 0,
            "trans_kind" => 39, // مازاد انبار گردانی
            "warehouse_id" => $warehouse_handling->warehouse_id,
            "status_id" => 500000200, // تایید شده
            "ic" => $warehouse_handling->warehouse->ic ?? (1 / 0)
        ]);
        $output_form->getCode("DCEF");
        $output_form->getRandom();

        WarehouseHandlingForm::create([
            "warehouse_handling_id" => $warehouse_handling->id,
            "input_form_id" => $input_form->id,
            "output_form_id" => $output_form->id
        ]);


        // اضافه کردن فرم های بسته بندی به فرم های خروج و وورد
        foreach ($packing_list_warehouse_handling as $item) {
            switch ($item->status_id) {
                case 524000401: //خوانده شده داخل انبار

                    break;
                case 524000402: //خوانده شده خارج از انبار
                    self::AddPackingFormToFrom($warehouse_handling, $item->packing_form, $input_form, $user_id);

                    break;
                case 524000403: //خوانده نشده (داخل انبار)
                    self::AddPackingFormToFrom($warehouse_handling, $item->packing_form, $output_form, $user_id);
                    break;


                case 524000401: //خوانده شده در انتظار تایید انبار
                    // در ردیف های قبلی ورود آن را ثبت  کردیم.
                    break;

            }
        }

        // حذف فرم ها اگر خالی هستند.
        if ($input_form->item()->count() == 0) {
            $input_form->delete();
        } else {
            event(new PutInWarehouseEvent($input_form));
        }

        if ($output_form->item()->count() == 0) {
            $output_form->delete();
        } else {
            event(new PutInWarehouseEvent($output_form));
        }


        // تغییر وضعیت انبار گردانی
        $warehouse_handling->status_id = 524000322; // تایید شده
        $warehouse_handling->save();


        // پردازش نهایی
        event(new WarehouseHandlingEvent($warehouse_handling, 524000307, "", $user_id));
        WarehouseProductBlock::UpdateBlockedByWarehouseHandling($warehouse_handling);
        return [
            "result" => true
        ];
    }

    public static function AddPackingFormToFrom(WarehouseHandling $warehouseHandling, PackingForm $packingForm, Form $form, $user_id)
    {

        if ($packingForm->packing_form_contents()->count() == 0) {
            // بسته بندی فرعی ندارد.
            foreach ($packingForm->items as $packing_form_item) {
                self:: AddFormItem($form, $packingForm, $packing_form_item, $warehouseHandling);
            }
        } else {
            foreach ($packingForm->packing_form_contents as $child_packing_form) {
                foreach ($child_packing_form->items as $packing_form_item) {
                    self:: AddFormItem($form, $packingForm, $packing_form_item, $warehouseHandling);
                }
            }
        }

        if ($packingForm->packing_form_contents()->count() > 0) {
            if ($form->trans_kind == 38) { //  38, // کسری انبار گردانی ورود
                $packingForm->warehouse_status_id = 4201;
                $packingForm->warehouse_id = $warehouseHandling->warehouse_id;
                $packingForm->save();
            } else { // مازاد انبارگردانی
                $packingForm->warehouse_status_id = 4202;
                $packingForm->warehouse_id = null;
                $packingForm->save();

            }
        }

        if ($form->trans_kind == 38) { //  38, // کسری انبار گردانی ورود
            $packingForm->status_id = 7007003;
            $packingForm->save();
            event(new PackingLogEvent($packingForm, 7007029, null, "", $form->id, $user_id));
        } else { // مازاد انبارگردانی
            $packingForm->status_id = 7007012;
            $packingForm->save();
            event(new PackingLogEvent($packingForm, 7007030, null, "", $form->id, $user_id));

        }


    }

    public static function AddFormItem(Form $form, PackingForm $packingForm, PackingFormItem $packing_form_item, WarehouseHandling $warehouseHandling)
    {
        $form_item = FormItem::create([
            "form_id" => $form->id,
            "product_id" => $packing_form_item->product_id,
            "amount" => $packing_form_item->final_amount,
            "sub_amount" => $packing_form_item->sub_amount,
            "carrier_id" => $packing_form_item->packing_form->carrier_id,
            "degree_id" => $packing_form_item->degree_id,
            "lot_number_id" => $packing_form_item->lot_number_id,
            "packing_type_id" => $packing_form_item->packing_form->packing_type_id,
            "packing_form_item_id" => $packing_form_item->id,
            "io_line_code" => 1,
            "description" => $form->trans_kind_item->caption . "(انبارگردانی شماره " . $warehouseHandling->id . ") " .
                "با کد بسته بندی " . ($packingForm->getCode()) .
                " و " . " فرم انبار " . ($form->code ?? "")
        ]);
    }

    public static function HasErrorInPackingForm(WarehouseHandling $warehouseHandling)
    {
        $valid_status = [524000401, 524000402, 524000403, 524000408]; /// وضعیت های معتبر

        $count = $warehouseHandling->packing_forms()->whereNotIn("status_id", $valid_status)->count();
        return $count != 0 ? true : false;
    }
}
