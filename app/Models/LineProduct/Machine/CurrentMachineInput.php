<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOMItemEnteringType;
use App\Models\Production\Production;
use App\Models\Utility\Script\Script1007\Script1007AllocationMaterialAmount;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class CurrentMachineInput extends Model
{
    use HasFactory;
    use Loggable;

    protected $table = "current_machine_inputs";
    protected $fillable = [
        "lot_number_id",
        "input_band_id",
        "input_line_code",
        "input_line_code_from",
        "input_line_code_to",
        "material_id",
        "product_id",
        "machine_id",
        "production_id",
        "band_code",
        "number",
        "amount",
        "percent_of_use",
        "allocation_id",
        "goods_kind_id",
        "priority_number",
        "consume_warehouse_id",
        "request_warehouse_id",
        "replacement_status_id",
        "bill_of_material_entering_type_id"
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function production()
    {
        return $this->belongsTo(Production::class);
    }

    public function bill_of_material_entering_type() {
        return $this->belongsTo( BOMItemEnteringType::class, "bill_of_material_entering_type_id", "id" );
    }
    public function carrier()
    {
        return $this->belongsTo(Carrier::class);
    }

    public function lot_number()
    {
        return $this->belongsTo(LotNumber::class);
    }

    public function consume_warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function request_warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function material()
    {
        return $this->belongsTo(Product::class, "material_id", "id");
    }

    public function goods_kind()
    {
        return $this->belongsTo(GoodsKind::class);
    }


    public function packing_form()
    {
        return $this->belongsTo(PackingForm::class);
    }

    public function input_band()
    {
        return $this->belongsTo(MachineTypeInputBand::class);
    }

    public function current_machine_input_logs()
    {
        return $this->hasMany(CurrentMachineInputLog::class);
    }

    public function amount_delivered($type = "allocation_material_amount")
    {
        if ($type == "allocation_material_amount") {
            return Product\ProductRequest\ProductRequestFromAllocation::where([
                "allocation_id" => $this->allocation_id,
                "material_id" => $this->material_id,
            ])->sum("amount_delivered");
        }
    }

    public function product_request_form_allocation_list()
    {
        return Product\ProductRequest\ProductRequestFromAllocation::where([
            "allocation_id" => $this->allocation_id,
            "material_id" => $this->material_id,
        ])->get();
    }

    public function getBandCodeList()
    {

        $list = CurrentMachineMaterialFlow::where([
            "allocation_id" => $this->allocation_id,
            "product_id" => $this->product_id,
            "material_id" => $this->material_id,
            "input_band_id" => $this->input_band_id,
            "input_line_code" => $this->input_line_code
        ])->get();
        $output = "";
        if ($list) {
            foreach ($list as $item) {
                $output .= $item->band_code . ",";
            }
        }

        return trim($output, ",");

    }

    public function amount_for_one_unit()
    {
        return CurrentMachineInput::getConsumedAmount($this->amount, $this->number, $this->percent_of_use);
    }

    public static function CreateOrUpdate(
        $input_band_id,
        $input_line_code,
        $material_id,
        $amount,
        $amount_required,
        $percent_of_use,
        $goods_kind_id,
        $allocation_id,
        $machine_id,
        $product_id,
        $production_id,
        $band_code,
        $number,
        $input_line_code_from,
        $input_line_code_to,
        $consume_warehouse_id,
        $request_warehouse_id,
        $bom_item,
        $bill_of_material_entering_type_id
    )
    {


        $current_input = CurrentMachineInput::firstOrCreate([
            "input_band_id" => $input_band_id,
            "input_line_code" => $input_line_code,
            "input_line_code_from" => $input_line_code_from,
            "input_line_code_to" => $input_line_code_to,
            "material_id" => $material_id,
            "goods_kind_id" => $goods_kind_id,
            "allocation_id" => $allocation_id,
            "machine_id" => $machine_id,
            "product_id" => $product_id,
            "production_id" => $production_id,
            "consume_warehouse_id" => $consume_warehouse_id,
            "request_warehouse_id" => $request_warehouse_id,
        ]);

        $current_input->band_code = -1;
        $current_input->amount = $amount;
        $current_input->amount_required = $amount_required;
        $current_input->percent_of_use = $percent_of_use;
        $current_input->number = $number; // پیش فرض یک است
        $current_input->bill_of_material_entering_type_id = $bill_of_material_entering_type_id;
        $current_input->save();

        CurrentMachineMaterialFlow::create([
            "allocation_id" => $allocation_id,
            "product_id" => $product_id,
            "material_id" => $material_id,
            "input_band_id" => $input_band_id,
            "input_line_code" => $input_line_code,
            "goods_kind_id" => $goods_kind_id,
            "band_code" => $band_code
        ]);


        // ذخیره کردن اطلاعات درجه های مجاز (در صورتی که bom حذف یا تغییر کرد، از درجه های در زمان تخصیص استفاده می شود.)
        CurrentMachineInputMaterialDegree::AddDegreeFromBOM($allocation_id, $product_id, $material_id, $bom_item);


    }

    public static function DeleteData($allocation_id)
    {
        CurrentMachineInput::where("allocation_id", $allocation_id)->delete();
        CurrentMachineMaterialFlow::where("allocation_id", $allocation_id)->delete();
        CurrentMachineInputMaterialDegree::where("allocation_id", $allocation_id)->delete();
    }

    public static function getAmountRequired($graph_link = null, $allocation = null,$production_id = null)
    {
        return $amount_required =
            CurrentMachineInput::getConsumedAmount(
                $graph_link->bom_item->amount, // مقدار مصرف
                $graph_link->bom_item->number, // تعداد مصرف
                $graph_link->bom_item->percent_of_use    // درصد مصرف
            ) *
            $allocation->items()->where("production_id",$production_id)->sum("allocation_amount") // جمع کل تخصیص
            ;
    }

    public static function getConsumedAmount($amount, $number, $percent_of_use)
    {
        return
            $amount * // مقدار مصرف
            $number * // تعداد مصرف
            $percent_of_use / 100;
    }


    /**
     * @param \App\Models\LineProduct\Machine\Allocation $allocation
     * چک کردن اینکه موجودی، تعداد بسته بندی و ... در انبارک ماشین موجود است.
     * @return string
     */
    public static function CheckInventoryAndPackingForms(Allocation $allocation)
    {
        $current_input_list = CurrentMachineInput::
        where("allocation_id", $allocation->id)->
        with("material")->
        get();
        $machine = $allocation->machine;
        $message = "";
        foreach ($current_input_list as $current_input) {
            // اگر کالایی انبار ندارد، لازم نیست که موجودی آن چک شود
            if ($current_input->material->warehouse_storage_type_id == 1) {
                // بدون انبارش لازم نیست که موجود چک شود.
                continue;
            }

            // بررسی موجودی انبارک
            $inventory = WarehouseProduct::getProductInventoryList([$current_input->material_id], null, $machine->warehouse_id);

            if ($inventory[$current_input->material_id] < $current_input->amount_required) {
                $message .= "موجودی " . $current_input->material->caption . "  در انبارک ماشین کافی نمی باشد." . "<br/>";
            }

            // بررسی تعداد بسته بندی های موجود در انبارک
            //تعداد بوبین ها
            $packing_form_count_0 = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
            join("packing_type_product", "packing_type_product.product_id", "packing_form_item.product_id")->
            where("packing_form_item.product_id", $current_input->material_id)->
            where("warehouse_id", $machine->warehouse_id)->
            where("packing_forms.status_id", 7007003)->
            where("packing_forms.warehouse_status_id", 4201)->
            where("sub_packing_form_number", 0)->count();


            $packing_form_count_n = PackingFormItem::join("packing_forms", "packing_forms.id", "packing_form_id")->
            join("packing_type_product", "packing_type_product.product_id", "packing_form_item.product_id")->
            where("packing_form_item.product_id", $current_input->material_id)->
            where("warehouse_id", $machine->warehouse_id)->
            where("packing_forms.status_id", 7007003)->
            where("packing_forms.warehouse_status_id", 4201)->
            where("sub_packing_form_number", "!=", 0)->
            distinct("packing_forms.id")->
            sum("sub_packing_form_number");

            if ($packing_form_count_0 + $packing_form_count_n < $current_input->number) {
                $message .= "تعداد بسته بندی " . $current_input->material->caption . "  در انبارک ماشین کافی نمی باشد." . "<br/>";
            }
        }
        if ($message == "") {
            return ["result" => true];
        } else {
            return ["result" => false, "error" => $message];
        }
    }
}
