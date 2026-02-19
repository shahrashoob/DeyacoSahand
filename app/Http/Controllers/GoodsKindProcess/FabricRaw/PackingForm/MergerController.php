<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Utility\Script\Script1012Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Machine\Allocation\Modification\MachineAllocationModification;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Order\TransKind;
use App\Models\Utility\Pdf;
use App\Models\Utility\Printer\PrinterFile;
use App\Models\Utility\Printer\PrinterFiles;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess\FabricRaw;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;
use Psy\Util\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MergerController extends Controller
{

    // ادغام یک بسته بندی در بسته بندی دیگر
    public static $info = [
        "route" => "fabric_raw.packing_form.merger.",
        "enable_status" => [
            "003",
            "005",
            "010"
        ],
        "button" => ["caption" => "ادغام بسته بندی", "class" => "btn-primary"],

    ];
    public static $view_path = "goods_kind_process.fabric_raw.packing_form.merger.";

    var $dashboard_route = "fabric_raw.packing_form.";

    public function __construct()
    {
    }

    public function index(PackingForm $packing_form)
    {


        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }


        return view(MergerController::$view_path . "index",
            compact("packing_form"));
    }

    public function submit(Request $request, PackingForm $packing_form)
    {

        $result = $this->checkPermission($packing_form);
        if ($result != "") {
            return $result;
        }

        $master_packing_form = PackingForm::where("code", "DCPK/" . $request->code)->first();

        $result = $this->cheekPermissionForMerge($packing_form, $master_packing_form);

        if ($result != "") {
            return $result;
        }

        if ($packing_form->packing_type->first_packing_type) {

            $result = self::WithSubPackingMerge($master_packing_form, $packing_form);
            if ($result["result"]) {
                return redirect()->route($this->dashboard_route . "index")->with(["success" => "عملیات با موفقیت انجام شد."]);
            } else {
                return back()->withErrors($result["error"]);
            }
        } else {

            $result = self::NormalMerge($master_packing_form, $packing_form);
            if ($result["result"]) {
                return redirect()->route($this->dashboard_route . "index")->with(["success" => "عملیات با موفقیت انجام شد."]);
            } else {
                return back()->withErrors($result["error"]);
            }
        }

        1 / 0;

        return redirect()->route("fabric_raw.packing_form.view", $packing_form)->with(["success" => "جهت دریافت لیبل پرینت شده، به محل پرینتر شماره 1 مراجعه فرمایید."]);
    }

    //مرج عادی: بسته بندی فرعی برای بسته بندی ها وجود ندارد.
    public static function NormalMerge(PackingForm $packing_form, PackingForm $merge_packing_form, $type = "MergePackingFrom")
    {
        switch ($type) {
            case "MergePackingFrom":
                $trans_kind_input = 103;
                $trans_kind_output = 203;
                break;
            case "AddToReservoir":
                $trans_kind_input = 103;
                $trans_kind_output = 203;
                break;
        }
        $packing_form_content = $packing_form->packing_form_contents()->count();
        if ($packing_form_content > 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه بسته بندی " . $packing_form->code . " دارای بسته بندی فرعی غیر مجاز است، امکان مرج وجود ندارد."
            ];
        }
        $merge_packing_form_content = $merge_packing_form->packing_form_contents()->count();
        if ($merge_packing_form_content > 0) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه بسته بندی " . $merge_packing_form->code . " دارای بسته بندی فرعی غیر مجاز است، امکان مرج وجود ندارد."
            ];
        }

        if ($packing_form->warehouse) {
            $input_form = self::InputForm($merge_packing_form->warehouse_id, $trans_kind_input);
            $output_form = self::OutputForm($packing_form->warehouse_id, $trans_kind_output);
        }


        foreach ($merge_packing_form->items as $merge_packing_form_item) {

            $new_packing_form_item_info = $merge_packing_form_item->toArray();
            $new_packing_form_item_info["production_form_item_id"] = null;
            $new_packing_form_item_info["production_form_item_lot_number_id"] = null;
            $new_packing_form_item_info["packing_form_id"] = $packing_form->id;


            switch ($type) {
                case "MergePackingFrom":

                    $new_packing_form_item = PackingFormItem::create($new_packing_form_item_info);
                    break;
                case "AddToReservoir": // اضافه کردن به مخزن اگر مخزن آن کالا را داشت به آن اضافه می شود و اگر نداشت یک ردیف ایجاد می کنیم

                    $new_packing_form_item = $packing_form->items()->where("product_id", $merge_packing_form_item->product_id)->first();


                    if (!$new_packing_form_item) {
                        $new_packing_form_item = PackingFormItem::create($new_packing_form_item_info);

                    }
                    else{
                        $new_packing_form_item->final_amount+=$merge_packing_form_item ->final_amount;
                        $new_packing_form_item->sub_amount+=$merge_packing_form_item ->sub_amount;
                        $new_packing_form_item->save();
                    }
                    break;
            }



            if ($packing_form->warehouse) {
                self::CreateFormItem($input_form, $new_packing_form_item, $packing_form, $merge_packing_form_item ->final_amount, $merge_packing_form_item->sub_amount);

                self::CreateFormItem($output_form, $merge_packing_form_item, $merge_packing_form,$merge_packing_form_item ->final_amount, $merge_packing_form_item->sub_amount);

            }
        }

        $merge_packing_form->status_id = 7007021; // ادغام شده
        $merge_packing_form->save();
        //حذف بسته بندی با ادغام
        event(new PackingLogEvent($merge_packing_form, 7007023, null, $packing_form->code,
            $output_form->id ?? null));


        // اضافه کردن وزن خالص و ناخالص
        $packing_form->weight += $merge_packing_form->weight;
        $result_merge_packing_type = PackingType::getWeight($merge_packing_form->packing_type, $merge_packing_form->carrier);
        if ($result_merge_packing_type["result"]) {
            $packing_form->gross_weight += $merge_packing_form->gross_weight - $result_merge_packing_type["weight"];
        }
        $packing_form->sub_packing_form_number += $merge_packing_form->sub_packing_form_number;
        $packing_form->save();

        //اضافه شدن آیتم با ادغام بسته بندی
        event(new PackingLogEvent($packing_form, 7007024, null, $merge_packing_form->code,
            $input_form->id ?? null));

        if (isset($input_form)) {
            event(new PutInWarehouseEvent($output_form));
            event(new PutInWarehouseEvent($input_form));
        }

        return ["result" => true];

    }

    public static function WithSubPackingMerge(PackingForm $packing_form, PackingForm $merge_packing_form)
    {
        $packing_form_content = $packing_form->packing_form_contents()->count();

        $merge_packing_form_content = $merge_packing_form->packing_form_contents()->count();

        $packing_form_product_ids = $packing_form->items()->distinct("product_id")->count();
        $merger_product_ids = $merge_packing_form->items()->distinct("product_id")->count();

        if ($packing_form_product_ids != 1 || $merger_product_ids != 1) {
            return [
                "result" => false,
                "error" => "با توجه به تنوع کالاهای موجود در بسته بندی امکان ادغام وجود ندارد، لطفا با پشتیبانی تماس بگیرید."
            ];
        }


        if ($packing_form_content == 0 && $merge_packing_form_content == 0) {
            // برای هر دو بسته بندی فرعی ایجاد نشده است.
            return self::WithSubPackingMergeCase00($packing_form, $merge_packing_form);
        } else if (($packing_form_content == 0 && $merge_packing_form_content > 0) || ($packing_form_content > 0 && $merge_packing_form_content == 0)) {

            return self::WithSubPackingMergeCaseN0($packing_form, $merge_packing_form);
        } else {
            return self::WithSubPackingMergeCaseNN($packing_form, $merge_packing_form);
        }

    }

    public static function WithSubPackingMergeCase00(PackingForm $packing_form, PackingForm $merge_packing_form)
    {
        // برای هر دو بسته بندی فرعی ایجاد نشده است.

        if ($packing_form->warehouse) {
            $input_form = self::InputForm($packing_form->warehouse_id);
            $output_form = self::OutputForm($packing_form->warehouse_id);
        }

        $merge_packing_form_item = $merge_packing_form->items()->first();
        $packing_form_item = $packing_form->items()->first();


        $packing_form_item->amount += $merge_packing_form_item->amount;
        $packing_form_item->amount_after_control += $merge_packing_form_item->amount_after_control;
        $packing_form_item->final_amount += $merge_packing_form_item->final_amount;
        $packing_form_item->sub_amount += $merge_packing_form_item->sub_amount;
        $packing_form_item->save();

        if ($packing_form->warehouse) {
            self::CreateFormItem($input_form, $packing_form_item, $packing_form, $merge_packing_form_item->final_amount, $merge_packing_form_item->sub_amount);

            self::CreateFormItem($output_form, $merge_packing_form_item, $merge_packing_form, $merge_packing_form_item->final_amount, $merge_packing_form_item->sub_amount);

        }


        $merge_packing_form->status_id = 7007021; // ادغام شده
        $merge_packing_form->save();

        //حذف بسته بندی با ادغام
        event(new PackingLogEvent($merge_packing_form, 7007023, null, $packing_form->code,
            $output_form->id ?? null));


        // اضافه کردن وزن خالص و ناخالص
        $packing_form->weight += $merge_packing_form->weight;
        $result_merge_packing_type = PackingType::getWeight($merge_packing_form->packing_type, $merge_packing_form->carrier);
        $packing_form->gross_weight += $merge_packing_form->gross_weight - $result_merge_packing_type["weight"];
        $packing_form->sub_packing_form_number += $merge_packing_form->sub_packing_form_number;
        $packing_form->save();

        //اضافه شدن آیتم با ادغام بسته بندی
        event(new PackingLogEvent($packing_form, 7007024, null, $merge_packing_form->code,
            $input_form->id ?? null));

        if (isset($input_form)) {
            event(new PutInWarehouseEvent($output_form));
            event(new PutInWarehouseEvent($input_form));
        }

        return ["result" => true];
    }

    public static function WithSubPackingMergeCaseN0(PackingForm $packing_form, PackingForm $merge_packing_form)
    {

        if ($packing_form->packing_type->create_sub_packing_form_in_creation) {
            // باید برای بسته بندی که بسته بندی فرعی ندارد، بسته بندی فرعی ایجاد کنیم و به موجودی انبار اضافه کنیم و بعد مثل مورد NN عمل کنیم.
            return [
                "result" => false,
                "error" => "ادغام بسته بندی برای حالتی که ایجاد بسته بندی های فرعی فعال باشد، پیاده سازی نشده است، لطفا با پشتیبانی تماس بگیرید."
            ];

        } else {
            // باید برای بسته بندی هایی که بسته بندی فرعی دارد، آنها را از انبار خارج کنیم و بعد مثل مورد 00 عمل کنیم.

            // حذف بسته بندی های فرعی packing_form
            if ($packing_form->packing_form_contents()->count() > 0) {
                if ($packing_form->warehouse) {
                    $output_form = self::OutputForm($packing_form->warehouse_id);
                    $input_form = self::InputForm($packing_form->warehouse_id);
                }

                self::RemoveSubPackingFormFromWarehouse($packing_form, $output_form, $input_form);


            }


            // حذف بسته بندی های فرعی merge_packing_form
            if ($merge_packing_form->packing_form_contents()->count() > 0) {
                if ($merge_packing_form->warehouse && !isset($input_form)) {
                    $output_form = self::OutputForm($merge_packing_form->warehouse_id);
                    $input_form = self::InputForm($merge_packing_form->warehouse_id);
                }

                self::RemoveSubPackingFormFromWarehouse($merge_packing_form, $output_form, $input_form);

            }

            // ثبت تراکنش های انبار
            if (isset($input_form)) {
                event(new PutInWarehouseEvent($output_form));
                event(new PutInWarehouseEvent($input_form));
            }

            //حالا که هر دو بسته بندی از نوع بدون بسته بندی فرعی شدند تابع 00 را اجرا می کنیم.

            return self:: WithSubPackingMergeCase00($packing_form, $merge_packing_form);

        }
    }

    public static function WithSubPackingMergeCaseNN(PackingForm $packing_form, PackingForm $merge_packing_form)
    {

        // اضافه کردن وزن خالص و ناخالص
        $packing_form->weight += $merge_packing_form->weight;
        $result_merge_packing_type = PackingType::getWeight($merge_packing_form->packing_type, $merge_packing_form->carrier);
        $packing_form->gross_weight += $merge_packing_form->gross_weight - $result_merge_packing_type["weight"];
        $packing_form->sub_packing_form_number += $merge_packing_form->sub_packing_form_number;
        $packing_form->save();


        foreach ($merge_packing_form->packing_form_contents as $content_packing_form) {
            $content_packing_form->packing_form_master_id = $packing_form->id;
            $content_packing_form->save();

        }

        $merge_packing_form->status_id = 7007021; // ادغام شده
        $merge_packing_form->warehouse_id = null;
        $merge_packing_form->warehouse_status_id = 4202; // خارج شده شده
        $merge_packing_form->save();


        $merge_packing_form_item = $merge_packing_form->items()->first();
        $packing_form_item = $packing_form->items()->first();


        $packing_form_item->amount += $merge_packing_form_item->amount;
        $packing_form_item->amount_after_control += $merge_packing_form_item->amount_after_control;
        $packing_form_item->final_amount += $merge_packing_form_item->final_amount;
        $packing_form_item->sub_amount += $merge_packing_form_item->sub_amount;
        $packing_form_item->save();

        //حذف بسته بندی با ادغام
        event(new PackingLogEvent($merge_packing_form, 7007023, null, $packing_form->code));

        //اضافه شدن آیتم با ادغام بسته بندی
        event(new PackingLogEvent($packing_form, 7007024, null, $merge_packing_form->code, $input_form->id ?? null));


        return ["result" => true];
    }

    public static function RemoveSubPackingFormFromWarehouse(PackingForm $packing_form, $output_form = null, $input_form = null)
    {

        foreach ($packing_form->packing_form_contents as $content_packing_form) {

            // خارج کردن بسته بندی های فرعی
            foreach ($content_packing_form->items as $content_packing_form_item) {

                if ($packing_form->warehouse) {
                    self::CreateFormItem($output_form,
                        $content_packing_form_item,
                        $content_packing_form,
                        $content_packing_form_item->final_amount,
                        $content_packing_form_item->amount
                    );
                }
            }

            if ($packing_form->warehouse) {
                $content_packing_form->status_id = 7007021;// ادغام شده
                $content_packing_form->packing_form_master_id = null;// قطع ارتباط مستر و بسته بندی فرعی
                $content_packing_form->save();
            }

        }
        // وارد کردن آیتم های بسته بندی جدید
        foreach ($packing_form->items as $packing_form_item) {

            if ($packing_form->warehouse) {
                self::CreateFormItem($input_form,
                    $packing_form_item,
                    $packing_form,
                    $packing_form_item->final_amount,
                    $packing_form_item->amount
                );
            }
        }
    }

    public static function InputForm($warehouse_id, $trans_kind = 103)
    {
        // فرم ورود تراکنش تغییر درجه
        $input_form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "production_card_id" => 0,
            "user_id" => Auth::id(),
            "form_type_id" => 304,
            "trans_kind" => $trans_kind, // ورود ادغام بسته بندی
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000200, // تایید شده
            "ic" => "",
        ]);
        $input_form->getCode();

        return $input_form;
    }

    public static function OutputForm($warehouse_id, $trans_kind = 203)
    {


        // برگ خروج تراکنش تغییر درجه
        $output_form = Form::CreateFrom([
            "order_id" => 0,
            "order_list_id" => 0,
            "user_id" => Auth::id(),
            "trans_kind" => $trans_kind,// خروج ادغام بسته بندی (سامانه)
            "warehouse_id" => $warehouse_id,
            "status_id" => 500000200, // تایید شده
            "ic" => "",
        ]);
        $output_form->getCode();

        return $output_form;
    }

    public static function CreateFormItem(
        Form $form, PackingFormItem $packing_form_item, PackingForm $packing_form, $amount, $sub_amount
    )
    {
        return $form_item = FormItem::create([
            "form_id" => $form->id,
            "product_id" => $packing_form_item->product_id,
            "amount" => $amount,
            "sub_amount" => $sub_amount,
            "carrier_id" => $packing_form_item->packing_form->carrier_id,
            "degree_id" => $packing_form_item->degree_id,
            "lot_number_id" => $packing_form_item->lot_number_id,
            "packing_type_id" => $packing_form_item->packing_form->packing_type_id,
            "packing_form_item_id" => $packing_form_item->id,
            "io_line_code" => 1,
            "description" => $form->trans_kind_item->caption .
                "با کد بسته بندی " . ($packing_form->getCode()) .
                " و " . " فرم ورود " . ($input_form->code ?? "")
        ]);
    }

    public function cheekPermissionForMerge(PackingForm $packing_form, PackingForm $merger_packing_form)
    {

        if (!$merger_packing_form) {
            return back()->withErrors("کد بسته بندی جهت ادغام در سامانه وجود ندارد.");
        }

        if ($merger_packing_form->id == $packing_form->id) {
            return back()->withErrors(" یک بسته بندی نمی تواند با خودش ادغام شود.");
        }
        if ($merger_packing_form->status_id != $packing_form->status_id) {
            return back()->withErrors(" وضعیت بسته بندی دو بسته جهت ادغام شبیه به هم نیست.");
        }


        if ($merger_packing_form->packing_type_id != $packing_form->packing_type_id) {

            if ($merger_packing_form->packing_type->first_packing_type) {

                if ($merger_packing_form->packing_type->first_packing_type_id != $packing_form->packing_type->first_packing_type_id) {
                    // اگر بسته بندی های سطح یک هم شبیه به هم بود مشکلی ندارد.
                    return back()->withErrors(" نوع بسته بندی دو بسته جهت ادغام شبیه به هم نیست.");
                }

            } else {
                return back()->withErrors(" نوع بسته بندی دو بسته جهت ادغام شبیه به هم نیست.");
            }

        }

        $last_layer = $merger_packing_form->packing_type->layers()->orderByDesc("layer_code")->first();
        if (!$last_layer) {
            return back()->withErrors(" لایه های بسته بندی یافت نشد.");
        }

        $carrier_type = $last_layer->carrier_type;
        if ($carrier_type) {
            $max_amount = $carrier_type->max_band_capacity * $carrier_type->max_band_number;
            if ($max_amount < $merger_packing_form->getFinalAmount() + $packing_form->getFinalAmount()) {
                return back()->withErrors("با توجه به ظرفیت حامل، امکان ادغام بسته بندی ها وجود ندارد");
            }
        }


        if (($merger_packing_form->warehouse->id ?? 0) != ($packing_form->warehouse->id ?? 0)) {
            return back()->withErrors(" انبار دو بسته جهت ادغام شبیه به هم نیست.");
        }
        if ($packing_form->warehouse->warehouse_type_id == 2) {
            return back()->withErrors("امکان ادغام بسته بندی های داخل انبارک وجود ندارد.");
        }

        if (($merger_packing_form->warehouse_status_id ?? 0) != ($packing_form->warehouse_status_id ?? 0)) {
            return back()->withErrors(" وضعیت قرار گیری در انبار دو بسته جهت ادغام شبیه به هم نیست.");
        }

        if (!in_array($merger_packing_form->warehouse_status_id, [4201, 4202])) {
            return back()->withErrors("وضعیت قرار گیری بسته بندی ها در انبار معتبر نمی باشد.");
        }

        if (($merger_packing_form->carrier->id ?? 0) != ($packing_form->carrier->id ?? 0)) {
            return back()->withErrors(" حامل دو بسته جهت ادغام شبیه به هم نیست.");
        }

        $result = PackingType::getWeight($merger_packing_form->packing_type, $merger_packing_form->carrier);
        if (!$result["result"]) {
            return back()->withErrors($result);
        }


        $packing_form_product_ids = $packing_form->items()->distinct("product_id")->orderBy("product_id")->pluck('product_id')->toArray();
        $merger_product_ids = $merger_packing_form->items()->distinct("product_id")->orderBy("product_id")->pluck('product_id')->toArray();

        if (count(array_diff($packing_form_product_ids, $merger_product_ids)) +
            count(array_diff($merger_product_ids, $packing_form_product_ids)) > 0) {
            return back()->withErrors("از آنجایی که کالای قرار گرفته در بسته بندی ها متفاوت است،
             امکان ادغام دو بسته بندی وجود ندارد.");
        }

        if (count($packing_form_product_ids) != 1) {
            return back()->withErrors("با توجه به تنوع کالا، امکان ادغام دو بسته بندی وجود ندارد.");
        }

        $packing_form_degree_ids = $packing_form->items()->distinct("degree_id")->orderBy("degree_id")->pluck('degree_id')->toArray();
        $merger_degree_ids = $merger_packing_form->items()->distinct("degree_id")->orderBy("degree_id")->pluck('degree_id')->toArray();

        if (count(array_diff($packing_form_degree_ids, $merger_degree_ids)) +
            count(array_diff($merger_degree_ids, $packing_form_degree_ids)) > 0) {
            return back()->withErrors("از آنجایی که درجه قرار گرفته در بسته بندی ها متفاوت است،
             امکان ادغام دو بسته بندی وجود ندارد.");
        }

        $packing_form_lot_number_ids = $packing_form->items()->distinct("lot_number_id")->orderBy("lot_number_id")->pluck('lot_number_id')->toArray();
        $merger_lot_number_ids = $merger_packing_form->items()->distinct("lot_number_id")->orderBy("lot_number_id")->pluck('lot_number_id')->toArray();

        if (count(array_diff($packing_form_lot_number_ids, $merger_lot_number_ids)) +
            count(array_diff($merger_lot_number_ids, $packing_form_lot_number_ids)) > 0) {
            return back()->withErrors("از آنجایی که لات کالاهای قرار گرفته در بسته بندی ها متفاوت است،
             امکان ادغام دو بسته بندی وجود ندارد.");
        }

        return "";
    }

    public function checkPermission(PackingForm $packing_form)
    {

        $result = FabricRaw\PackingFormController::checkPermissionConditions($packing_form, MergerController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }

}
