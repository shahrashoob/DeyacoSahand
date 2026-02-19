<?php

namespace App\Http\Controllers\Production\PublicModule;

use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PagePrintingController;
use App\Imports\ProductionModule1Import;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Production\ProductionFormItemLotNumber;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\QueueOfLargeOperation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;

class UploadController
{
// production/public_module/register_production/
    public static $info = [
        "route" => "production.public_module.upload.",
        "view_path" => "production.public_module.upload.",
    ];
    var $view_path;
    var $route_path;
    var $max_of_row_packng_form = 50;

    //
    public function __construct()
    {
        $this->route_path = self::$info["route"];
        $this->view_path = self::$info["view_path"];
    }

    public function index(MachineAllocation $machine_allocation)
    {
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }

        // کارت های تولید باید دقیقا یک بسته بندی داشته باشند
        foreach ($machine_allocation->allocation->items as $machine_allocation_item) {

            $production = $machine_allocation_item->production;
            $packing_count = $production->packing_types()->count();
            if (!$packing_count) {
                return back()->withErrors("با توجه به اینکه کارت " . $production->serial() . " دارای $packing_count بسته بندی است، استفاده از بارگذاری فایل اکسل امکان پذیر نیست.");
            }
            $new_packing_type = $production->packing_types()->first()->packing_type;
            if (!isset($packing_types[$machine_allocation_item->product_id])) {
                $packing_types[$machine_allocation_item->product_id] = $new_packing_type;
            } elseif ($packing_types[$machine_allocation_item->product_id]->id != $new_packing_type->id) {
                return back()->withErrors("با توجه به اینکه نوع بسته بندی برای کالای " .
                    $production->product->caption .
                    " در کارت های مختلف یکسان نیست، امکان بارگذاری از طریق فایل وجود ندارد.");
            }
        }

        // آیا وجود فرم ورود به انبار تحویل نشده در زمان ثبت تولید توسط این پیمانکار/ماشین چک شود، (در صورت T بودن، باید همه بسته بندی ها تحویل به انبار شده باشند.)
        $result_checking_form_not_delivered = RegisterProductionController::CheckingFormNotDelivered($machine_allocation);

        if (!$result_checking_form_not_delivered["result"]) {

            $message_add_packing = $result_checking_form_not_delivered["error"];


            return back()->withErrors($message_add_packing);

        }
        return view($this->view_path . ".index", ["machine_allocation" => $machine_allocation]);
    }

    public function submit(MachineAllocation $machine_allocation)
    {
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        $packing_types = [];
        foreach ($machine_allocation->allocation->items as $machine_allocation_item) {

            $production = $machine_allocation_item->production;
            $packing_count = $production->packing_types()->count();
            if (!$packing_count) {
                return back()->withErrors("با توجه به اینکه کارت " . $production->serial() . " دارای $packing_count بسته بندی است، استفاده از بارگذاری فایل اکسل امکان پذیر نیست.");
            }
            $packing_types[$machine_allocation_item->product_id] = $production->packing_types()->first()->packing_type;
        }


        $PMI = new ProductionModule1Import();
        $PMI->allocation_id = $machine_allocation->allocation_id;
        $PMI->user_id = Auth::id();
        $PMI->packing_types = $packing_types;


        Excel::import($PMI, request()->file('file_uploaded'));
        return redirect()->route($this->route_path . "preview", $machine_allocation);
    }

    public function preview(MachineAllocation $machine_allocation)
    {
        $result = $this->checkPermission($machine_allocation);
        if ($result != "") {
            return $result;
        }
        $data = JsonDataList::GetData($machine_allocation->allocation_id, 700);
        if (isset($data["error"]) && $data["error"] && (!isset($data["product_ids"]) || count($data["product_ids"]) == 0)) {
            return back()->withErrors($data["error"]);
        }

        if (!isset($data["products"])) {
            return back()->withErrors("اطلاعات شناسه کالا یافت نشد، لطفا یکبار دیگر تلاش کنید.");
        }
        $product_ids = $data["product_ids"];
        $products = Product::whereIn("id", $product_ids)->get()->keyBy("id");


        return view($this->view_path . ".preview", ["data" => $data, "machine_allocation" => $machine_allocation, "products" => $products]);
    }

    public function confirm(Request $request, MachineAllocation $machine_allocation_parent)
    {
        $result = $this->checkPermission($machine_allocation_parent);
        if ($result != "") {
            return $result;
        }
        $data = JsonDataList::GetData($machine_allocation_parent->allocation_id, 700);

        $product_ids = $data["product_ids"];
        //  $products = Product::whereIn("id", $product_ids)->get()->keyBy("id");
        $controller = new RegisterProductionController();

        $current_amount_list = MachineAllocationPackingForm::
        join("packing_form_item", "packing_form_item.packing_form_id", "machine_allocation_packing_form.packing_form_id")->
        where([
            "machine_allocation_packing_form.status_id" => 7007006, // بسته های معلق
        ])->
        whereIn("machine_allocation_id", $machine_allocation_parent->allocation->items()->pluck("id")->toArray())->
        groupBy("machine_allocation_id")->
        selectRaw("machine_allocation_id, sum(final_amount) as final_amount")->
        pluck("final_amount", "machine_allocation_id")->
        toArray();
        // لیست انواع بسته بندی ها
        $packing_types = [];
        $machine_allocation_amount_remaining = [[]];
        $machine_allocation_list = [];
        foreach ($machine_allocation_parent->allocation->items as $machine_allocation_item) {

            $production = $machine_allocation_item->production;
            $packing_count = $production->packing_types()->count();
            if (!$packing_count) {
                return back()->withErrors("با توجه به اینکه کارت " . $production->serial() . " دارای $packing_count بسته بندی است، استفاده از بارگذاری فایل اکسل امکان پذیر نیست.");
            }

            $packing_types[$machine_allocation_item->product_id] = $production->packing_types()->first()->packing_type;
            $first_packing_type = $packing_types[$machine_allocation_item->product_id];


// بررسی حداکثر مقدار قابل تولید به ازای هر کارت تولید
            // بررسی حداکثر مقدار مجاز قابل تولید
            $current_amount = isset($current_amount_list[$machine_allocation_item->id]) ? $current_amount_list[$machine_allocation_item->id] : 0;
            $result_check_max = RegisterProductionController::CheckProductionTerminate($machine_allocation_item, "check_max", $current_amount);
            if (!$result_check_max["result"]) {
                $message_add_packing = $result_check_max["error"];
            }


            $machine_allocation_amount_remaining[$machine_allocation_item->product_id][$machine_allocation_item->production_id] = $machine_allocation_item->allocation_amount - $current_amount;


            $machine_allocation_list[$machine_allocation_item->production_id] = $machine_allocation_item;

        }


        //گرفتن فرم تولید در حال تولید
        $production_form = ProductionForm::join("production_form_item", "production_forms.id", "production_form_id")->
        where([
                "allocation_id" => $machine_allocation_parent->allocation_id,
                "production_forms.status_id" => $controller->getStatus($machine_allocation_parent, "production_form_in_production"),
            ]
        )->
        select("production_forms.*")->
        first();

        if (!$production_form) {

            // تعریف فرم تولید جدید
            $production_form = ProductionForm::AddNewForm(
                $machine_allocation_parent->machine->id ?? null,
                null,
                0,
                $first_packing_type->id,
                $machine_allocation_parent->contractor->id ?? null,
                $controller->getStatus($machine_allocation_parent, "production_form_in_production"));


            $production_form = ProductionForm::find($production_form->id);
        }


        $production_form_item_list = [];
        $production_form_item_lot_number_list = [[]];
        $packing_form_ids_for_print = [];
        foreach ($data["packing_form_list"] as $packing_form_row) {

            // در هر بار که می خواهیم بسته بندی را به کارت تولید اضافه کنیم، بسته بندی را به کارتی اضافه می کنیم که مقدار باقی مانده آن از همه بزرگتر باشد.
            $maxValue = max($machine_allocation_amount_remaining[$packing_form_row["product_id"]]);

            $max_machine_allocation_id = array_search($maxValue, $machine_allocation_amount_remaining[$packing_form_row["product_id"]]);

            $machine_allocation = $machine_allocation_list[$max_machine_allocation_id];


            // اگر فرم در حال تکمیل، بیش از یک آیتم داشت، اخرین آیتم را بر می داریم.
            if (!isset($production_form_item_list[$machine_allocation->production_id])) {
                $production_form_item = $production_form->items()->where("production_id", $machine_allocation->production_id)->orderBy("id", "desc")->first();

                if (!$production_form_item) {
                    $production_form_item = ProductionFormItem::AddNewItem(
                        $machine_allocation->allocation_id,
                        $production_form->id,
                        $machine_allocation->production_id,
                        $machine_allocation->product_id,
                        1,
                        $controller->getStatus($machine_allocation, "production_form_in_production"),// در حال تولید
                        0,
                        $machine_allocation->version_code ?? null
                    );

                }
                $production_form_item_list[$machine_allocation->production_id] = $production_form_item;
            }

            $production_form_item = $production_form_item_list[$machine_allocation->production_id];

            if (!isset($production_form_item_lot_number_list[$production_form_item->id][$packing_form_row["lot_number_id"]])) {
                //تعریف لات برای فرم تولید در صورتی که قبلا تعریف نشده
                $production_form_item_lot_number = ProductionFormItemLotNumber::where([
                    "production_form_id" => $production_form->id,
                    "production_form_item_id" => $production_form_item->id,
                    "lot_number_id" => $packing_form_row["lot_number_id"]
                ])->first();

                if (!$production_form_item_lot_number) {
                    $production_form_item_lot_number = ProductionFormItemLotNumber::create([
                        "production_form_id" => $production_form->id,
                        "production_form_item_id" => $production_form_item->id,
                        "lot_number_id" => $packing_form_row["lot_number_id"]
                    ]);
                }

                $production_form_item_lot_number_list[$production_form_item->id][$packing_form_row["lot_number_id"]] = $production_form_item_lot_number;
            }

            $production_form_item_lot_number = $production_form_item_lot_number_list[$production_form_item->id][$packing_form_row["lot_number_id"]];

            $packing_form = PackingForm::create([
                "carrier_id" => $carrier->id ?? null,
                "status_id" => 7007011, // "معلق - api"
                "packing_type_id" => $packing_form_row["packing_type_id"],
                "weight" => $packing_form_row["weight"],
                "gross_weight" => $packing_form_row["gross_weight"],
            ]);

            $final_amount = $packing_form_row["amount"];
            $sub_amount = $packing_form_row["sub_amount"];
            $sub_amount2 = $packing_form_row["sub_amount2"];


            // مقدار نهایی را از مقدار تخصیص کم می کنیم تا در مرحله بعد تخصیص دیگری را انتخاب نماید.
            $machine_allocation_amount_remaining[$packing_form_row["product_id"]][$max_machine_allocation_id] -= $final_amount;


            $production_form_item_lot_number->amount = $production_form_item_lot_number->amount + $final_amount;
            $production_form_item_lot_number->save();

            $packing_form_item = PackingFormItem::create([
                "packing_form_id" => $packing_form->id,
                "final_amount" => $final_amount,
                "amount" => $final_amount,
                "amount_after_control" => $final_amount,
                "sub_amount" => $sub_amount ?? 0,
                "sub_amount2" => $sub_amount2,
                "init_sub_amount" => $sub_amount ?? 0,
                "status_id" => $packing_form->status_id,
                "product_id" => $machine_allocation->product_id,
                "degree_id" => $packing_form_row["degree_id"],
                "lot_number_id" => $packing_form_row["lot_number_id"],
                "production_form_item_id" => $production_form_item->id,
                "band_code" => 1,
            ]);

            // اگر مالک کارت تولید الگوریتم پین دارد، کد پین وارد شده را ارسال می کنیم وگر نه که هیچ
            $packing_form->getRandom($packing_form_row["pin1"]);

            $packing_form_ids_for_print[] = $packing_form->id;

            MachineAllocationPackingForm::create([
                "machine_id" => $machine_allocation->machine_id,
                "contractor_id" => $machine_allocation->contractor_id,
                "machine_allocation_id" => $machine_allocation->id,
                "packing_form_id" => $packing_form->id,
                "need_to_complete_information" => 0
            ]);

        }

        if ($request->print_packing_forms) {

// ثبت درخواست پرینت بسته بندی ها
            $data = [
                "packing_form_ids" => $packing_form_ids_for_print,
                "worker_id" => Auth::id(),
            ];

            QueueOfLargeOperation::AddToQueue($data, 300);
        }


        JsonDataList::RemoveData($machine_allocation_parent->allocation_id, 700);

        return redirect()->route("production.public_module.register_production.index", $machine_allocation_parent)->with(["success" => "لیست بسته بندی ها با موفقیت ثبت گردید."]);
    }

    public
    function checkPermission(MachineAllocation $machine_allocation)
    {
        $controller = new RegisterProductionController();
        return $controller->checkPermission($machine_allocation);

    }
}