<?php

namespace App\Models\LineProduct\Machine;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Models\Contractor\Contractor;
use App\Models\Form\Form;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation\AllocationDoffs;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Fault\CurrentMachineFault;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\BOM\BOMItem;
use App\Models\LineProduct\Product\BOM\BOMReplace;
use App\Models\Order\Order;
use App\Models\Order\OrderPackingForm;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Status;
use App\Models\Utility\Unit\UnitType;
use App\Models\Warehouse\WarehouseProduct;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Allocation extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = ["machine_id", "status_id", "contractor_id", "supplier_id", "order_id", "allocation_unit_type_id"];

    public function items()
    {
        $items = $this->hasMany(MachineAllocation::class);

        return $items;
    }

    public function forms()
    {
        $items = $this->hasMany(Form::class);

        return $items;
    }
    public function status()
    {
       return $this->belongsTo(Status::class);


    }

    public function allocation_doffs()
    {
        return $this->hasMany(AllocationDoffs::class);
    }

    public function code()
    {
        return $this->id;
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }


    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function allocation_unit_type(Product $product)
    {
        switch ($this->allocation_unit_type_id) {
            case 1:
                return $product->unit->caption;
            case 2:
                return $product->sub_unit->caption;
            case 3:
                return $product->sub_unit2->caption;
            case 4:
                return "بسته بندی";
            default:
                return "***";
        }
    }

    public function get_caption_of_allocation_unit_type()
    {
        return $this->belongsTo(UnitType::class);
    }

    public function material_consumed()
    {
        return $this->hasMany(MachineAllocationMaterialConsumed::class);
    }

    public function get_partical_datetime()
    {
        if (!$this->predict_of_production_start_date_practical) {
            return "";
        }

        return jdate(Carbon::parse($this->predict_of_production_start_date_practical)->timestamp)->format('H:i Y/m/d ');

    }

    public function get_theory_datetime()
    {
        if (!$this->predict_of_production_start_date_theory) {
            return "";
        }

        return jdate(Carbon::parse($this->predict_of_production_start_date_theory)->timestamp)->format('H:i Y/m/d ');

    }

    function partical_houre()
    {
        if (!$this->predict_of_production_time_practical) {
            return "";
        }

        return "'" . (int)($this->predict_of_production_time_practical / 60) . ":" . ($this->predict_of_production_time_practical % 60);

    }

    function getAllocationAmount()
    {
        switch ($this->allocation_unit_type_id) {
            case 1:
                return $this->items()->sum("allocation_amount");
            case 2:
                return $this->items()->sum("allocation_sub_amount");
                break;
            case 3:
                1 / 0;
            case 4:
                return $this->items()->sum("number_of_packing_form");
        }

    }

    public function theory_houre()
    {
        if (!$this->predict_of_production_time_theory) {
            return "";
        }

        return "'" . (int)($this->predict_of_production_time_theory / 60) . ":" . ($this->predict_of_production_time_theory % 60);

    }

    public function before_allocation($production_type_id = false)
    {
// اول از بین کالاهای روزر بررسی می کنیم که تخصیص قبلی آن چیست

        $reserve_allocation = Allocation::
        join("machine_allocation", "allocation_id", "allocations.id")->
        join("production_cards", "production_cards.id", "production_id")->
        where("allocations.machine_id", $this->machine_id)->
        where("allocations.id", "<", $this->id)->
        where("allocations.status_id", 5310040)->
        when($production_type_id, function ($query) use ($production_type_id) {
            return $query->where("production_type_id", $production_type_id);
        })->
        orderByDesc("allocations.id")->
        select("allocations.*")->
        first();
        if ($reserve_allocation) {
            return $reserve_allocation;
        }
// اگر تخصیص قبلی از بین کالاهای رزور نبود، از بین تخصیص هایی که تولید شده اند انتخاب کن
        return Allocation::
        join("machine_allocation", "allocation_id", "allocations.id")->
        join("production_cards", "production_cards.id", "production_id")->
        where("allocations.machine_id", $this->machine_id)->
        where("allocations.id", "<", $this->id)->
        where("allocations.status_id", "!=", 5310005)->
        where("allocations.status_id", "!=", 5310030)->
        where("allocations.status_id", "!=", 5310040)->
        when($production_type_id, function ($query) use ($production_type_id) {
            return $query->where("production_type_id", $production_type_id);
        })->
        orderByDesc("production_start_date")->
        select("allocations.*")->
        first();


    }

    public static function getDifferentTowAllocation_old($before_allocation, Allocation $current_allocation, $goods_kind_caption_en)
    {
        if ($goods_kind_caption_en != "Fabric_Raw") {
            1 / 0;
        }

        $machine_module_type = $current_allocation->machine->machine_type->machine_module_type;

        $value["has_design_change"] = false;
        $value["has_product_change"] = false;
        $value["has_yarn_weft_change"] = false;
        $value["has_warps_change"] = false;
        $value["has_weft_density_change"] = false;
        $value["has_article_change"] = false;
        $value["has_bar_fabric_change"] = false;
        if (!isset($before_allocation)) {
            $value["has_design_change"] = true;
            $value["has_product_change"] = true;
            $value["has_yarn_weft_change"] = true;
            $value["has_warps_change"] = true;
            $value["has_weft_density_change"] = true;
            $value["has_article_change"] = true;
            $value["has_bar_fabric_change"] = false;

        }
        // برای محاسبه تغییر فقط با باند 1 مقایسه می شود.
        // Product_change
        $before_machine_allocation = MachineAllocation::
        where("allocation_id", $before_allocation->id ?? 0)->
        where("band_code", 1)->first();

        $current_machine_allocation = MachineAllocation::
        where("allocation_id", $current_allocation->id)->
        where("band_code", 1)->first();

        if (!isset($before_machine_allocation)) {
            $value["has_design_change"] = false;
            $value["has_product_change"] = true;
        }

        $value["before_product"] = $before_machine_allocation->product ?? null;
        $value["current_product"] = $current_machine_allocation->product;

        // شماره اولین BOM کالای قبلی
        $before_bom = isset($value["before_product"]) ? $value["before_product"]->get_first_bom_from_route($before_allocation->machine ?? null) : null;
        $value["before_bom"] = $before_bom;


        // شماره اولین BOM کالای جاری
        $current_bom = $value["current_product"]->get_first_bom_from_route($current_allocation->machine);
        $value["current_bom"] = $current_bom;

        if (isset($before_machine_allocation)) {
            $value["has_product_change"] =
                $before_machine_allocation->product_id !=
                $current_machine_allocation->product_id;
        }
        //  محصول عوض نشده
        if (!$value["has_product_change"]) {
            $value["has_design_change"] = false;
            $value["has_weft_density_change"] = false;
            $value["has_yarn_weft_change"] = false;
            $value["has_warps_change"] = false;
            $value["has_article_change"] = false;
            $value["has_bar_fabric_change"] = false;
        }

        // طراحی عوض شده؟
        $checklist = MachineModuleType::getChecklist($machine_module_type->id, "design_change");

        $value["checklist_design"] = $checklist;

        $value["property_caption"] = GoodsKindProperty::whereIn("id", $checklist)->orderBy("priority_number")->pluck("caption", "id");

        $value["current_property_value_product"] =
        $current_property_value_product = GoodsKindPropertyValue::
        where("product_id", $current_machine_allocation->product_id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value", "goods_kind_property_id")->toArray();

        if (isset($before_machine_allocation)) {
            $value["before_property_value_product"] =
            $before_property_value_product = GoodsKindPropertyValue::
            where("product_id", $before_machine_allocation->product_id)->
            whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
            pluck("value", "goods_kind_property_id")->toArray();
        }

        if (isset($before_machine_allocation)) {
            $value["has_design_change"] =
                count(array_diff($current_property_value_product, $before_property_value_product)) +
                count(array_diff($before_property_value_product, $current_property_value_product)) > 0;
        }

        // بررسی اینکه چله تغییر کرده یا خیر

        if (isset($before_machine_allocation)) {
            $before_warps_bom = BOMItem::join("products", "material_id", "products.id")->
            where("bill_of_material_id", $before_bom->id ?? 0)->
            where("product_id", $before_machine_allocation->product_id)->
            where("goods_kind_id", 3)->
            orderBy("material_id")->
            pluck("material_id")->
            toArray();
        }


        $current_warps_bom = BOMItem::join("products", "material_id", "products.id")->
        where("bill_of_material_id", $current_bom->id ?? 0)->
        where("product_id", $current_machine_allocation->product_id)->
        where("goods_kind_id", 3)->
        orderBy("material_id")->
        pluck("material_id")->
        toArray();

        if (isset($before_machine_allocation)) {
            $value["has_warps_change"] =
                count(array_diff($before_warps_bom, $current_warps_bom)) +
                count(array_diff($current_warps_bom, $before_warps_bom)) > 0;
        }

        // بررسی اینکه نخ پود تغییر کرده یا خیر
        if (isset($before_machine_allocation)) {
            $before_yarn_weft_bom = BOMItem::join("products", "material_id", "products.id")->
            where("bill_of_material_id", $before_bom->id ?? 0)->
            where("product_id", $before_machine_allocation->product_id)->
            where("goods_kind_id", 2)->
            pluck("material_id")->
            toArray();
        }

        $current_yarn_weft_bom = BOMItem::join("products", "material_id", "products.id")->
        where("bill_of_material_id", $current_bom->id ?? 0)->
        where("product_id", $current_machine_allocation->product_id)->
        where("goods_kind_id", 2)->
        pluck("material_id")->
        toArray();

        if (isset($before_machine_allocation)) {
            $value["has_yarn_weft_change"] =
                count(array_diff($before_yarn_weft_bom, $current_yarn_weft_bom)) +
                count(array_diff($current_yarn_weft_bom, $before_yarn_weft_bom)) > 0;
        }

        // بررسی اینکه تراکم تغییر کرده یا خیر
        $checklist = MachineModuleType::getChecklist($machine_module_type->id, "density_change");;


        $value["weft_density_caption"] = GoodsKindProperty::whereIn("id", $checklist)->orderBy("priority_number")->pluck("caption", "id");

        $value["current_weft_density_value_product"] =
        $current_weft_density_value_product = GoodsKindPropertyValue::
        where("product_id", $current_machine_allocation->product_id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value", "goods_kind_property_id")->toArray();

        if (isset($before_machine_allocation)) {
            $value["before_weft_density_value_product"] =
            $before_weft_density_value_product = GoodsKindPropertyValue::
            where("product_id", $before_machine_allocation->product_id)->
            whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
            pluck("value", "goods_kind_property_id")->toArray();
        }

        if (isset($before_machine_allocation)) {
            $value["has_weft_density_change"] =
                count(array_diff($before_weft_density_value_product, $current_weft_density_value_product)) +
                count(array_diff($current_weft_density_value_product, $before_weft_density_value_product)) > 0;
        }

        /**
         * بررسی اینکه طرح تغییر کرده یا خیر
         */
        $checklist = MachineModuleType::getChecklist($machine_module_type->id, "article_change");;

        $value["article_caption"] = GoodsKindProperty::whereIn("id", $checklist)->orderBy("priority_number")->pluck("caption", "id");

        $value["current_article_value_product"] =
        $current_article_value_product = GoodsKindPropertyValue::
        where("product_id", $current_machine_allocation->product_id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value", "goods_kind_property_id")->toArray();

        if (isset($before_machine_allocation)) {
            $value["before_article_value_product"] =
            $before_article_value_product = GoodsKindPropertyValue::
            where("product_id", $before_machine_allocation->product_id)->
            whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
            pluck("value", "goods_kind_property_id")->toArray();
        }

        if (isset($before_machine_allocation)) {
            $value["has_article_change"] =
                count(array_diff($before_article_value_product, $current_article_value_product)) +
                count(array_diff($current_article_value_product, $before_article_value_product)) > 0;
        }

        // بررسی اینکه عرض شانه عوض شده یا خیر
        $bar_fabrice_property = MachineProductProperties::find(1);
        $current_bar_fabrice = MachineProductProperties::getPropertyValue(
            $bar_fabrice_property,
            $current_machine_allocation->machine->machine_type_id,
            $current_machine_allocation->product_id, false
        );
        $value["current_bar_fabrice"] = $current_bar_fabrice;
        if (isset($before_machine_allocation)) {

            $before_bar_fabrice = MachineProductProperties::getPropertyValue(
                $bar_fabrice_property,
                $before_machine_allocation->machine->machine_type_id,
                $before_machine_allocation->product_id, false
            );

            $value["before_bar_fabrice"] = $before_bar_fabrice;

            $value["has_bar_fabric_change"] =
                $current_bar_fabrice != $before_bar_fabrice;
        }


        return $value;
    }

    public static function getDifferentTowAllocation($before_allocation, Allocation $current_allocation, $goods_kind_caption_en)
    {
        if ($goods_kind_caption_en != "Fabric_Raw") {
            1 / 0;
        }

        $machine_module_type = $current_allocation->machine->machine_type->machine_module_type;

        $value["has_design_change"] = false;
        $value["has_product_change"] = false;
        $value["has_yarn_weft_change"] = false;
        $value["has_warps_change"] = false;
        $value["has_weft_density_change"] = false;
        $value["has_article_change"] = false;
        $value["has_bar_fabric_change"] = false;
        $value["step"] = "A";
        if (!isset($before_allocation)) {
            $value["has_design_change"] = true;
            $value["has_product_change"] = true;
            $value["has_yarn_weft_change"] = true;
            $value["has_warps_change"] = true;
            $value["has_weft_density_change"] = true;
            $value["has_article_change"] = true;
            $value["has_bar_fabric_change"] = false;
            $value["step"] .= "B";
        }
        // برای محاسبه تغییر فقط با باند 1 مقایسه می شود.
        // Product_change
        $before_machine_allocation = MachineAllocation::
        where("allocation_id", $before_allocation->id ?? 0)->
        where("band_code", 1)->first();

        $current_machine_allocation = MachineAllocation::
        where("allocation_id", $current_allocation->id)->
        where("band_code", 1)->first();

        if (!isset($before_machine_allocation)) {
            $value["has_design_change"] = false;
            $value["has_product_change"] = true;
            $value["step"] .= "C";
        }

        $value["before_product"] = $before_machine_allocation->product ?? null;
        $value["current_product"] = $current_machine_allocation->product;

        if (isset($before_machine_allocation)) {
            $value["has_product_change"] =
                $before_machine_allocation->product_id !=
                $current_machine_allocation->product_id;
        }
        //  محصول عوض نشده
        if (!$value["has_product_change"]) {
            $value["has_design_change"] = false;
            $value["has_weft_density_change"] = false;
            $value["has_yarn_weft_change"] = false;
            $value["has_warps_change"] = false;
            $value["has_article_change"] = false;
            $value["has_bar_fabric_change"] = false;
            $value["step"] .= "D";
        }

        // طراحی عوض شده؟
        $checklist = MachineModuleType::getChecklist($machine_module_type->id, "design_change");

        $value["checklist_design"] = $checklist;

        $value["property_caption"] = GoodsKindProperty::whereIn("id", $checklist)->orderBy("priority_number")->pluck("caption", "id");

        $value["current_property_value_product"] =
        $current_property_value_product = GoodsKindPropertyValue::
        where("product_id", $current_machine_allocation->product_id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value", "goods_kind_property_id")->toArray();

        if (isset($before_machine_allocation)) {
            $value["before_property_value_product"] =
            $before_property_value_product = GoodsKindPropertyValue::
            where("product_id", $before_machine_allocation->product_id)->
            whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
            pluck("value", "goods_kind_property_id")->toArray();
        }

        if (isset($before_machine_allocation)) {
            $value["has_design_change"] =
                count(array_diff($current_property_value_product, $before_property_value_product)) +
                count(array_diff($before_property_value_product, $current_property_value_product)) > 0;
            $value["step"] .= "E";
        }

        // بررسی اینکه چله تغییر کرده یا خیر

        if (isset($before_machine_allocation)) {
            $before_warps_bom =
                CurrentMachineInput::where("allocation_id", $before_allocation->id ?? 0)->
                where("goods_kind_id", 3)->
                orderBy("material_id")->
                pluck("material_id")->
                toArray();
        }


        $current_warps_bom = CurrentMachineInput::where("allocation_id", $current_allocation->id ?? 0)->
        where("goods_kind_id", 3)->
        orderBy("material_id")->
        pluck("material_id")->
        toArray();

        if (isset($before_machine_allocation)) {
            $value["has_warps_change"] =
                count(array_diff($before_warps_bom, $current_warps_bom)) +
                count(array_diff($current_warps_bom, $before_warps_bom)) > 0;
            $value["step"] .= "F";
        }

        // بررسی اینکه نخ پود تغییر کرده یا خیر
        if (isset($before_machine_allocation)) {
            $before_yarn_weft_bom = CurrentMachineInput::where("allocation_id", $before_allocation->id ?? 0)->
            where("goods_kind_id", 2)->
            pluck("material_id")->
            toArray();
        }

        $current_yarn_weft_bom = CurrentMachineInput::where("allocation_id", $current_allocation->id ?? 0)->
        where("goods_kind_id", 2)->
        pluck("material_id")->
        toArray();

        if (isset($before_machine_allocation)) {
            $value["has_yarn_weft_change"] =
                count(array_diff($before_yarn_weft_bom, $current_yarn_weft_bom)) +
                count(array_diff($current_yarn_weft_bom, $before_yarn_weft_bom)) > 0;
            $value["step"] .= "G";
        }

        // بررسی اینکه تراکم تغییر کرده یا خیر
        $checklist = MachineModuleType::getChecklist($machine_module_type->id, "density_change");;


        $value["weft_density_caption"] = GoodsKindProperty::whereIn("id", $checklist)->orderBy("priority_number")->pluck("caption", "id");

        $value["current_weft_density_value_product"] =
        $current_weft_density_value_product = GoodsKindPropertyValue::
        where("product_id", $current_machine_allocation->product_id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value", "goods_kind_property_id")->toArray();

        if (isset($before_machine_allocation)) {
            $value["before_weft_density_value_product"] =
            $before_weft_density_value_product = GoodsKindPropertyValue::
            where("product_id", $before_machine_allocation->product_id)->
            whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
            pluck("value", "goods_kind_property_id")->toArray();
        }

        if (isset($before_machine_allocation)) {
            $value["has_weft_density_change"] =
                count(array_diff($before_weft_density_value_product, $current_weft_density_value_product)) +
                count(array_diff($current_weft_density_value_product, $before_weft_density_value_product)) > 0;
        }

        /**
         * بررسی اینکه طرح تغییر کرده یا خیر
         */
        $checklist = MachineModuleType::getChecklist($machine_module_type->id, "article_change");;

        $value["article_caption"] = GoodsKindProperty::whereIn("id", $checklist)->orderBy("priority_number")->pluck("caption", "id");

        $value["current_article_value_product"] =
        $current_article_value_product = GoodsKindPropertyValue::
        where("product_id", $current_machine_allocation->product_id)->
        whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
        pluck("value", "goods_kind_property_id")->toArray();

        if (isset($before_machine_allocation)) {
            $value["before_article_value_product"] =
            $before_article_value_product = GoodsKindPropertyValue::
            where("product_id", $before_machine_allocation->product_id)->
            whereIn("goods_kind_property_id", $checklist)->orderBy("goods_kind_property_id")->
            pluck("value", "goods_kind_property_id")->toArray();
        }

        if (isset($before_machine_allocation)) {
            $value["has_article_change"] =
                count(array_diff($before_article_value_product, $current_article_value_product)) +
                count(array_diff($current_article_value_product, $before_article_value_product)) > 0;
            $value["step"] .= "W";
        }

        // بررسی اینکه عرض شانه عوض شده یا خیر
        $bar_fabrice_property = MachineProductProperties::find(1);
        $current_bar_fabrice = MachineProductProperties::getPropertyValue(
            $bar_fabrice_property,
            $current_machine_allocation->machine->machine_type_id,
            $current_machine_allocation->product_id, false
        );
        $value["current_bar_fabrice"] = $current_bar_fabrice;
        if (isset($before_machine_allocation)) {

            $before_bar_fabrice = MachineProductProperties::getPropertyValue(
                $bar_fabrice_property,
                $before_machine_allocation->machine->machine_type_id,
                $before_machine_allocation->product_id, false
            );

            $value["before_bar_fabrice"] = $before_bar_fabrice;

            $value["has_bar_fabric_change"] =
                $current_bar_fabrice != $before_bar_fabrice;
        }


        return $value;
    }

    public function setChangesFromBeforeAllocation($goods_kind_caption_en, $before_allocation = null)
    {
        if ($goods_kind_caption_en != "Fabric_Raw") {
            1 / 0;
        }
        if (!$before_allocation) {
            // گرفتن تخصیص قبلی
            $before_allocation = $this->before_allocation();
        }

        $value = Allocation::getDifferentTowAllocation($before_allocation, $this, $goods_kind_caption_en);
        $this->has_product_change = $value["has_product_change"];
        $this->has_design_change = $value["has_design_change"];
        $this->has_weft_density_change = $value["has_weft_density_change"];
        $this->has_yarn_weft_change = $value["has_yarn_weft_change"];
        $this->has_warps_change = $value["has_warps_change"];
        $this->has_article_change = $value["has_article_change"];
        $this->has_bar_fabric_change = $value["has_bar_fabric_change"];

        $this->save();

        return $this;
    }

    public function get_create_date()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('Y/m/d');
    }

    public function get_create_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i');
    }

    public function get_create_date_and_time()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    /**
     * @param \App\Models\LineProduct\Machine\Allocation $allocation
     * محاسبه حداکثر مقدرا قابل تخصیص با توجه به کارت های رزرو و جاری و موجودی انبار ها برای مواد اولیه
     *
     * @return mixed
     */

    public static function getMaxAllocationAmountAccordingToWarehouse(Allocation $allocation, $allocation_amount, $bom, $declared_inventory = [], $production_id_of_machine_allocation = null)
    {

        // حداکثر اولویت جایگزینی بین همه ردیف های BOM
        $max_priority_number_replace = BOMReplace::where(["bill_of_material_id" => $bom->id])->max("priority_number");
        $max_priority_number_bom = BOMItem::where(["bill_of_material_id" => $bom->id])->max("priority_number_in_replace");
        $max_priority_number = max($max_priority_number_replace, $max_priority_number_bom);

        // به دست آوردن همه جایشگت های مجاز
        //لیست آیتم های BOM در کالای اصلی
        $permutation_material_id_list[0] = $bom->items()->orderBy("material_id")->pluck("material_id")->toArray();
        $k = 1;
        foreach ($bom->permutations as $permutation_item) {
            // اگر جایگشت فعال است، انتخاب کن
            if ($permutation_item->active_status_id == 1200) {
                $permutation_material_id_list[$k] = $permutation_item->items()->orderBy("material_id")->pluck("material_id")->toArray();
                $k++;
            }
        }

        // اولویت کالای اصلی در مقایسه با کالای جایگزین
        $bom_item_priority_number_in_replace = $bom->items()->pluck("priority_number_in_replace", "material_id")->toArray();

        // لیست کالاهای جایگزنی که به صورت آرایه با  کلید روبرو می باشد : کد کالا - اولویت
        $bom_replace_list = BOMReplace::where(["bill_of_material_id" => $bom->id])->
        get()->
        keyBy(function ($item) {
            return $item["material_id"] . "_" . $item["priority_number"];
        });

        // ووردی های جاری ماشین
        $current_machine_input_list =
            CurrentMachineInput::where("allocation_id", $allocation->id)->
            when($production_id_of_machine_allocation, function ($query) use ($production_id_of_machine_allocation) {
                return $query->where("production_id", $production_id_of_machine_allocation);
            })->
            groupBy("material_id")->
            groupBy("input_band_id")->
            groupBy("input_line_code")->
            get()->
            keyBy(function ($item) {
                return $item["material_id"] . "_" . $item["input_band_id"] . "_" . $item["input_line_code"];
            });


        $material_priority = [];
        // برای راحتی اطلاعات کالای جایگزین و ورود ماشین را به صورت دیگری در این آرایه ذخیره می کنیم.
        $material_machine_input = [];
        $material_ids_all = [];
        foreach ($current_machine_input_list as $current_machine_input) {
            for ($k = 1; $k <= $max_priority_number; $k++) {

                $material_id = $current_machine_input->material_id;

                $material_priority[$current_machine_input->id][$k] =
                    [
                        "current_machine_input_id" => $current_machine_input->id,
                        "input_band_id" => $current_machine_input->input_band_id,
                        "input_line_code" => $current_machine_input->input_line_code,
                        "material_id" => $material_id,
                        "amount" => $current_machine_input->amount,
                        "number" => $current_machine_input->number,
                        "percent_of_use" => $current_machine_input->percent_of_use,
                        "amount_required" => CurrentMachineInput::getConsumedAmount(
                            $current_machine_input->amount,
                            $current_machine_input->number,
                            $current_machine_input->percent_of_use
                        )
                    ];;
                $material_ids_all[] = $material_id;
                if (!isset($material_machine_input[$material_id][$current_machine_input->id])) {
                    $material_machine_input[$material_id][$current_machine_input->id] = [
                        "material_id" => $material_id,
                        "priority_number" => $bom_item_priority_number_in_replace[$material_id],
                        "current_machine_input_id" => $current_machine_input->id
                    ];
                }

                if (isset($bom_replace_list[$material_id . "_" . $k])) {

                    $material_priority[$current_machine_input->id][$k] = [
                        "current_machine_input_id" => $current_machine_input->id,
                        "input_band_id" => $current_machine_input->input_band_id,
                        "input_line_code" => $current_machine_input->input_line_code,
                        "material_id" => $bom_replace_list[$material_id . "_" . $k]->replace_product_id,
                        // "bom_replace"=>$bom_replace_list[$material_id."_".$k],
                        "amount" => $bom_replace_list[$material_id . "_" . $k]->amount,
                        "number" => $bom_replace_list[$material_id . "_" . $k]->number,
                        "percent_of_use" => $bom_replace_list[$material_id . "_" . $k]->percent_of_use,
                        "amount_required" => CurrentMachineInput::getConsumedAmount(
                            $bom_replace_list[$material_id . "_" . $k]->amount,
                            $bom_replace_list[$material_id . "_" . $k]->number,
                            $bom_replace_list[$material_id . "_" . $k]->percent_of_use
                        )
                    ];

                    $replace_product_id = $bom_replace_list[$material_id . "_" . $k]->replace_product_id;

                    $material_ids_all[] = $replace_product_id;

                    $material_machine_input[$replace_product_id][$current_machine_input->id] = [
                        "material_id" => $material_id,
                        "priority_number" => $k,
                        "current_machine_input_id" => $current_machine_input->id
                    ];

                }
            }

        }


        // آیا برای این کالا موجودی انبار چک شود
        $send_product_request_form_by_robot =
            CurrentMachineInput::
            join("machine_type_input_band_goods_kind", "machine_type_input_band_id", "input_band_id")->
            where("send_product_request_form_by_robot", 1)->
            where("allocation_id", $allocation->id)->
            when($production_id_of_machine_allocation, function ($query) use ($production_id_of_machine_allocation) {
                return $query->where("production_id", $production_id_of_machine_allocation);
            })->
            groupBy("material_id")->
            pluck("send_product_request_form_by_robot", "material_id");


        // مقدار کل مورد نیاز مواد اولیه برای کارت رزرو و جاری
        $current_reserve_allocation_list = CurrentMachineInput::
        join("machines", "machine_id", "machines.id")->
        join("allocations", "allocations.id", "allocation_id")->
        whereIn("allocations.status_id", [5310040, 5310010])-> // تخصیص رزرو/ جاری
        whereIn("material_id", $material_ids_all)->
        where("machines.check_inventory_for_allocation", 1)->// فقط ماشین هایی که تیک دارند
        groupBy("allocation_id")->
        groupBy("material_id")->
        selectRaw("material_id,  sum(amount_required) as amount_required,allocation_id")->
        orderBy("material_id")->
        get();
// برای اینکه می خواستیم مقدار کارت رزرو و جاری را لاگ کنیم، به جای اینکه دو کوثری بزنیم، یک کوثری را با حله به دست می آوریم.
        $amount_required_list = [];
        foreach ($current_reserve_allocation_list as $amount_required_allocation) {
            if (!isset($amount_required_list[$amount_required_allocation->material_id])) {
                $amount_required_list[$amount_required_allocation->material_id] = 0;
            }
            $amount_required_list[$amount_required_allocation->material_id] +=
                round($amount_required_allocation->amount_required, 6);
        }

        //
// مقدار مصرف شده مواد اولیه کارت های جاری
        $consumed_amount = ProductionFormItem::
        join("allocations", "allocations.id", "allocation_id")->
        join("machines", "machine_id", "machines.id")->
        join("current_machine_inputs", "allocations.id", "current_machine_inputs.allocation_id")->
        where("allocations.status_id", 5310010)-> // تخصیص جاری
        whereIn("material_id", $material_ids_all)->
        where("machines.check_inventory_for_allocation", 1)->// فقط ماشین هایی که تیک دارند
        groupBy("material_id")->
        selectRaw("material_id,  sum( production_form_item.amount * current_machine_inputs.amount* current_machine_inputs.number * current_machine_inputs.percent_of_use /100 ) as consumed_amount ")->
        pluck("consumed_amount", "material_id");

        $material_list_all = Product::whereIn("id", $material_ids_all)->with("unit")->get()->keyBy("id");

        $inventory = WarehouseProduct::getProductInventoryList($material_ids_all);

        // موجودی که از طریق سیستم اعلام می شود، نسبت به موجودی انبار اولویت دارد.
        foreach ($declared_inventory as $product_id => $declared_inventory_item) {
            $inventory[$product_id] = $declared_inventory_item;
        }


        // $allocation_band = $allocation->items()->count();
        foreach ($current_machine_input_list as $current_machine_input) {

            for ($k = 1; $k <= $max_priority_number; $k++) {

                $priority_material_id = $material_priority[$current_machine_input->id][$k]["material_id"];


                // موجودی
                $material_priority[$current_machine_input->id][$k]["inventory"] =
                    isset($inventory[$priority_material_id]) ? $inventory[$priority_material_id] : 0;

                // مقدار مصرف شده رزرو
                $material_priority[$current_machine_input->id][$k]["consumed_amount"] =
                    isset($consumed_amount[$priority_material_id]) ? $consumed_amount[$priority_material_id] : 0;

                // مقدار مورد نیاز رزوو
                $material_priority[$current_machine_input->id][$k]["amount_required_reserve"] =
                    isset($amount_required_list[$priority_material_id]) ? $amount_required_list[$priority_material_id] : 0;

                /**
                 * مقدار قابل تولید با توجه به موجودی ماده material_id
                 * موجودی فعال (موجودی انبار - مقدار مورد نیاز کارت های جاری و رزرو + مقدار مصرف شده)
                 * موجودی فعال / مقدار = مقدار قابل بافت با توجه به مقدار ماده اولیه material_id
                 */
                $material_priority[$current_machine_input->id][$k]["active_inventory"] =
                    max(
                        $material_priority[$current_machine_input->id][$k]["inventory"] -

                        ($material_priority[$current_machine_input->id][$k]["amount_required_reserve"] -
                            $material_priority[$current_machine_input->id][$k]["consumed_amount"]
                        ),
                        0
                    );

                // مقداری از کالا که می توان با موجودی فعال تولید کرد
                $material_priority[$current_machine_input->id][$k]["amount_for_production"] =
                    max(
                        round($material_priority[$current_machine_input->id][$k]["active_inventory"]
                            /
                            self::GetAllRequiredAmountFromMaterialPriority($material_priority, $k, $material_priority[$current_machine_input->id][$k]["material_id"])

                            , 2
                        ),
                        0
                    );

                $pid = $material_priority[$current_machine_input->id][$k]["material_id"];
                $material_priority[$current_machine_input->id][$k]["product_code"] = $material_list_all[$pid]->code;

                if (!isset($send_product_request_form_by_robot[$current_machine_input->material_id])) {
                    $material_priority[$current_machine_input->id][$k]["amount_for_production"] = 99999999;
                }


            }

        }

        $min_production = 9999999999;
        $result = [
            "bom_id" => $bom->id,
            "amount_can_be_produced" => 0,
            "material" => []
        ];
        foreach ($current_machine_input_list as $item) {

            // اولین بار همه آنهایی را انتخاب می کنیم که اولویت 1 هستند.
            // و عدد اولویت را نگهداری می کنیم.
            $priority_number = 1;
            $material_priority[$item->id][$priority_number]["priority_number"] = $priority_number;
            $result["material"][$item->id] = $material_priority[$item->id][$priority_number];
//echo  $material_result["amount_production"]."<br/>";
            if ($material_priority[$item->id][1]["amount_for_production"] < $min_production) {
                $min_production = $material_priority[$item->id][1]["amount_for_production"];
            }


        }

        // مقدار تولید شده را از موجودی فعال کم می کنیم.
        foreach ($current_machine_input_list as $item) {

            $material_priority[$item->id][1]["amount_for_production"] -= $min_production;

        }

        $result["amount_can_be_produced"] = $min_production;

        // مقدار قابل تولید را به اندازی مینیم کل از همه کم می کنیم.
        foreach ($current_machine_input_list as $item) {

            $result["material"][$item->id]["material_amount_for_production"] =
                $min_production
                *
                $material_priority[$item->id][1]["amount_required"];


        }
        $end_result[0] = $result;

        // چک کردن اینکه این کالا جزء کالاهای مجاز تولید است یا خیر
        $result_check_end_result_index = self::CheckForEndResult($result, $permutation_material_id_list);
        if ($result_check_end_result_index == -1) {
            $end_result[0]["amount_can_be_produced"] = 0;
            $end_result[0]["not_allowed_for_production"] = 1;
        } else {
            // این حالت را از لیست خارج می کنیم، ممکن است کالای اصلی یا یکی از جایگشت ها باشد.
            unset($permutation_material_id_list[$result_check_end_result_index]);
        }


        // ************************  $min_production < $allocation_amount ==> user Replace Product

        // با توجه به اینکه مقدار های کالا برای تولید کارت تولید، کافی نیست، کالاهای جایگزین را بررسی می کنیم.
        if ($min_production < $allocation_amount) {


            // مقدار باقی مانده از کارت تولید
            $remaining_allocation_amount = $allocation_amount;


            $p = 0;
            // به ازای هر جایگزنی کالا یکی یکی پیش می رویم و خروجی هر مرجله را به ورودی مرحله بعدی می دهیم تا بتوانیم به ازای هر اولویت یک مقدار تخصیص به دست آوریم.
            // لیست همه SP ها را می گیریم
            foreach ($permutation_material_id_list as $permutation_material_id_item) {
                $p++;
                $priority_number = $p;



//return $material_machine_input;
                $end_result[$priority_number] = self::getMaxAllocationAmountAccordingToWarehouseBySPList(
                    $material_priority,
                    $permutation_material_id_item,
                    $material_machine_input,
                    $current_machine_input_list
                );


                // مقدار تولید شده را از موجودی فعال کم می کنیم.
                foreach ($current_machine_input_list as $item) {
                    if (isset($material_priority[$item->id][$priority_number]["amount_for_production"])) {
                        $material_priority[$item->id][$priority_number]["amount_for_production"] -= $end_result[$priority_number]["amount_can_be_produced"];
                    }
                }
            }
//            }

        }


        $permutation_list = Allocation::getPermutation($end_result, $allocation_amount,$bom->product);

        return [
            "permutation_list" => $permutation_list,// لیست حالت های قابل انتخاب
            "end_result" => $end_result, // لیست نتیحه به ازای حالت اصلی و به ازای هر الویوت
            "current_reserve_allocation_list" => $current_reserve_allocation_list
        ];


    }

    public static function CheckForEndResult($result, $bom_material_id_list)
    {
        //   return $result;

        $product_list = [];
        foreach ($result["material"] as $item) {
            $product_list[] = $item["material_id"];

        }
        //  return  $result["material"];
        sort($product_list);

//     return   $bom_material_id_list;
        $k = 0;
        foreach ($bom_material_id_list as $bom_material) {

            if (count(array_diff($product_list, $bom_material)) == 0 ||
                count(array_diff($bom_material, $product_list)) == 0) {
                // return $product_list;
                return $k;
            }
            $k++;
        }

        return -1;
    }

    /**
     * @param $amount_can_be_produced
     * @param $materials_info_list
     * @param $material_priority
     * @param $current_priority_number
     * @return array
     * این تابع در ابتدا نوشته شده بود برای بررسی جایگزین های تولید، که مشکل داشت و فقط برای حالتی که یک SP داشته باشیم جواب می داد.
     * یعنی اگر بیش از یک جایگزنی برای کالا بود فقط جایگزین اول را بررسی کند برای همه آن را تغییر دادیم و یک الگوریتم جدید نوشیتم.
     */
    public static function getMaxAllocationAmountAccordingToWarehouseByPriorityNumber(
        $amount_can_be_produced,
        $materials_info_list,
        $material_priority,
        $current_priority_number
    )
    {

        $result_new_priority = [
            "amount_can_be_produced" => 0,
            "material" => []
        ];

        // حداقل مقدار قابل تولید
        $min_production = 9999999999;
        $max_priority_number = $current_priority_number;
        $priority_changed = false;
        // به ازای مواد اولیه که از مرحله اولویت قبلی محاسبه شده، بررسی می کنیم که به ازای اولویت جدید می توان چیزی بیشتر تولید کرد یا خیر
        foreach ($materials_info_list as $current_machine_input_id => $material_info) {

            // اگر مقدار قابل تولید کارت تولید با یک ماده اولیه برابر با حد پایین تولید بود، جایگزینی را بررسی می کنیم.
            if ($material_info["amount_for_production"] == $amount_can_be_produced) {

                $priority_number = $material_info["priority_number"] + 1;
                $max_priority_number = $priority_number;

                $material_info_new_priority = $material_priority[$current_machine_input_id][$priority_number];

                // اگر مقدار قابل تولید جایگزین جدید از مقدار جایگزین قبلی  بیشتر بود، جابجا می کنیم.
                if ($material_info_new_priority["amount_for_production"] > 0) {

                    $priority_changed = true;

                    // حد پایین تولید را بروز رسانی می کنیم.
                    if ($material_info_new_priority["amount_for_production"] < $min_production) {
                        $min_production = $material_info_new_priority["amount_for_production"];

                    }

                    $material_info_new_priority["priority_number"] = $priority_number;
                    $result_new_priority["material"][$current_machine_input_id] = $material_info_new_priority;

                }


            }

            // اگر نتوانستیم، جایگزینی برای کالا انتخاب کنیم، همان قبلی را انتخاب می کنیم.
            if (!isset($result_new_priority["material"][$current_machine_input_id])) {

                $result_new_priority["material"][$current_machine_input_id] = $material_info;

// حد پایین تولید را بروز رسانی می کنیم.
                if ($material_info["amount_for_production"] < $min_production) {
                    $min_production = $material_info["amount_for_production"];

                }
            }

        }

        // اگر اولویت ها را یک پله افزایش دادیم، یعنی می توانیم با اولویت های جدید تولید کنیم وگر نه نمی توانیم هیچ تولید جدیدی داشته باشیم.
        if ($priority_changed) {
            $result_new_priority["amount_can_be_produced"] = $min_production;
        } else {
            // مقدار قابل تولید را صفر می کینم، چون هیچ پیشتهاد جدید ندادیم.
            $result_new_priority["amount_can_be_produced"] = 0;
        }


//        foreach ($result_new_priority["material"] as $current_machine_input_id => $material_info) {
//
//            // اولویت انتخاب شده، برای هر رسته کالایی فرق می کند.
//
//            $result_new_priority["material"][$current_machine_input_id]["material_amount_for_production"] =
//                $min_production
//                *
//                self::GetAllRequiredAmountFromMaterialPriority($material_priority, $max_priority_number, $material_info["material_id"]);
//
//        }

        return $result_new_priority;

    }


    /**
     *
     */
    public static function getMaxAllocationAmountAccordingToWarehouseBySPList(

        $materials_info_list,
        $permutation_material_id_item,
        $material_machine_input,
        $current_machine_input_list
    )
    {
        $min_production = 9999999999;
        $result_new_priority = [
            "amount_can_be_produced" => 0,
            "material" => []
        ];
        // به ازای هر آیتم جایگشت، ورودی آن را پیدا می کنیم و می بینیم در کدام اولویت قرار دارد.
        foreach ($permutation_material_id_item as $material_id) {

            foreach ($current_machine_input_list as $current_machine_input_item) {

                if (isset($material_machine_input[$material_id][$current_machine_input_item->id])) {
                    $material_machine_input_item = $material_machine_input[$material_id][$current_machine_input_item->id];
                    $priority_number = $material_machine_input_item["priority_number"];


                    $material_info_new_priority = $materials_info_list[$current_machine_input_item->id][$priority_number];

                    // اگر مقدار قابل تولید جایگزین جدید از مقدار جایگزین قبلی  بیشتر بود، جابجا می کنیم.

                    // حد پایین تولید را بروز رسانی می کنیم.
                    if ($material_info_new_priority["amount_for_production"] < $min_production) {
                        $min_production = $material_info_new_priority["amount_for_production"];

                    }

                    $material_info_new_priority["priority_number"] = $priority_number;
                    $result_new_priority["material"][$current_machine_input_item->id] = $material_info_new_priority;


                }

            }
        }

        // مقدار قابل تولید را برابر با مقدار مینیمم قرار می دهیم.
        $result_new_priority["amount_can_be_produced"] = $min_production;


//        foreach ($result_new_priority["material"] as $current_machine_input_id => $material_info) {
//
//            // اولویت انتخاب شده، برای هر رسته کالایی فرق می کند.
//
//            $result_new_priority["material"][$current_machine_input_id]["material_amount_for_production"] =
//                $min_production
//                *
//                self::GetAllRequiredAmountFromMaterialPriority($material_priority, $max_priority_number, $material_info["material_id"]);
//
//        }

        return $result_new_priority;
    }


    public static function GetAllRequiredAmountFromMaterialPriority($material_priority, $priority_number, $material_id)
    {
        $sum_amount_required_for_material = 0;
        foreach ($material_priority as $current_machine_input_priority_id => $material_priority_info) {

            if ($material_priority[$current_machine_input_priority_id][$priority_number]["material_id"] == $material_id) {
                $sum_amount_required_for_material +=
                    $material_priority[$current_machine_input_priority_id][$priority_number]["amount_required"];
            }

        }

        return $sum_amount_required_for_material;
    }


    /**
     * @param $data
     * به دست آوردن جایگشت های مختلف برای تولید کالا
     *
     * @return void
     */
    public static function getPermutation($end_result, $allocation_amount,$product)
    {

        $count_result = count($end_result);
        $permutation_list = []; // لیست جایگشت های مختلف | حالت های مختلف جهت تخصیص
        $p_number = 1;
        for ($k = 0; $k < $count_result; $k++) {

            if ($end_result[$k]["amount_can_be_produced"] == 0) {
                continue; // اگر یک انتخاب مقدار قابل تخصیص آن صفر بود، نباز نیست که بررسی شود.
            }

            $remaining_allocation_amount = $allocation_amount;
            $permutation = [];

            for ($i = $k; $i < $count_result; $i++) {
                //  echo $end_result[ $i ]["amount_can_be_produced"]."<br/>".$remaining_allocation_amount;
                if ($end_result[$i]["amount_can_be_produced"] > 0 and $remaining_allocation_amount > 0) {

                    $amount_can_be_produced=$end_result[$i]["amount_can_be_produced"];
                    if ($product && $product->frame_ratio_unit2 && $product->sub_unit2_id == 1400) {

                        $amount_can_be_produced= round($amount_can_be_produced/ $product->frame_ratio_unit2) * $product->frame_ratio_unit2;
                    }

                    $permutation[$i] = min($amount_can_be_produced, $remaining_allocation_amount);
                    $remaining_allocation_amount -= $permutation[$i];
                }
            }

            if ($remaining_allocation_amount == 0) {
                $permutation_list[$p_number] = $permutation;
                $p_number++;
            }

        }

        return $permutation_list;
    }

    public static function updatePriorityNumber(Machine $machine)
    {
        $reserve_list = $machine->ReserveAllocation()->orderBy("priority_number")->get();
        $priority_number = 1;
        foreach ($reserve_list as $allocation) {

            $allocation->priority_number = $priority_number;
            $allocation->save();

            $priority_number++;
        }

        $current_allocation = $machine->getCurrentAllocation();
        if ($current_allocation) {
            $current_allocation->priority_number = 0;
            $current_allocation->save();
        }
    }

    /**
     * @param \App\Models\LineProduct\Machine\Machine $machine
     * @param \App\Models\Production\Production $production
     * بررسی نقص های کارت تولید در زمان تخصیص
     * // لیست نقص های کالای یک سطح بالاتر را به دست می آوریم، ماشین نباید نقص فعالی از آن مجموعه داشته باشد اگر داشت
     * // اجازه تخصیص نمی دهد.
     *
     * @return array|bool[]
     */
    public static function CheckAllocationFault(Machine $machine, $production, $allocation_list = null, $current_machine_fault_ids = [])
    {


        $current_machine_faults = CurrentMachineFault::
        where("machine_id", $machine->id)->
        where("active_status_id", 1200)->
        get();

        // ماشین هیچ عیبی ندارد
        if (count($current_machine_faults) + count($current_machine_fault_ids) == 0) {
            return ["result" => true];
        }
        foreach ($current_machine_faults as $item) {
            $current_machine_fault_ids[] = $item->id;
        }

        if ($allocation_list) {
            // اگر لیستی از تخصیص ها را به تابع پاس بدهیم، به ترتیب آنها را بررسی می کند
            // به اولین تخصیص که مشکلی نداشت رسید، خروجی را ارسال می کند و تمام تخصیص هایی هم که قبل از آن بودند و مشکل داشتند را بر می گرداند.
            // بنابراین اگر اولین رزرو مشکلی نداشته باشد، الگوریتم پایان می یابد.
            $allocation_illegal_fault = [];
            foreach ($allocation_list as $allocation) {
                $allocation_item = $allocation->items()->first();
                $production = $allocation_item->production ?? null;

                if ($production) {
                    $result = self::CheckAllocationFault($machine, $production, null, $current_machine_fault_ids);
                    if (!$result["result"]) {
                        $allocation_illegal_fault[$allocation->id] = $allocation;
                    } else {

                        break;
                    }
                }
            }

            if (count($allocation_illegal_fault) == 0) {
                return ["result" => true];
            } else {
                return [
                    "result" => false,
                    "allocation_illegal" => $allocation_illegal_fault
                ];
            }


        } else {
            $product_fault_ids = Product\BOM\BOMFaultIllegal::
            where("product_id", $production->parent_production->product_id ?? 0)->
            pluck("product_fault_id")->
            toArray();

            // کالا هیچ عیب غیر مجازی ندارد
            if (count($product_fault_ids) == 0) {
                return ["result" => true];
            }


            $machine_fault_ids = MachineType\MachineTypeMachineFaultProductFault::
            whereIn("product_fault_id", $product_fault_ids)->
            where("machine_type_id", $machine->machine_type_id)->
            pluck("machine_fault_id")->
            toArray();


            // هیچ کدام از عیب های ماشین جزء عیب های غیر مجاز کالا نیست.
            if (count($machine_fault_ids) == 0) {
                return ["result" => true];
            }


            $current_machine_fault = CurrentMachineFault::
            where("machine_id", $machine->id)->
            whereIn("machine_fault_id", $machine_fault_ids)->
            where("active_status_id", 1200)->
            get();

            // بررسی عیب های جاری ماشین
            foreach ($current_machine_fault_ids as $current_machine_fault_id) {
                if (in_array($current_machine_fault_id, $machine_fault_ids)) {
                    return [
                        "result" => false,
                        "error" => "نقص اعلام شده جزء نقص های غیر مجاز ماشین برای کالا است."
                    ];
                }
            }

            // ماشین حداقل یک نقص غیر مجاز دارد
            if (count($current_machine_fault) > 0) {
                $message = "با توجه به اینکه نقص های زیر در " . $machine->caption . " فعال است امکان تخصیص وجود ندارد. " . "<br/>";
                foreach ($current_machine_fault as $item) {
                    $message .= $item->machine_fault->caption . "<br/>";
                }

                return [
                    "result" => false,
                    "error" => $message
                ];
            }

            return [
                "result" => true,
            ];
        }


    }


    /**
     * @param \App\Models\LineProduct\Machine\Allocation $allocation
     * @param \App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel $production_channel
     * در این تابع موجودی اعلامی توسط سامانه را با توجه به تخصیص قبلی به دست می آوریم.
     *
     * @return array|void
     */
    public static function GetDeclaredInventory(Allocation $allocation, ProductionChannel $production_channel)
    {

        $machine_allocation = MachineAllocation::
        where("allocation_id", $allocation->id ?? 0)->
        where("band_code", 1)->first();
        if (!$machine_allocation) {
            return [
                "result" => false,
                "error" => "آیتم های تخصیص " . $allocation->id . " یافت نشد."
            ];
        }

        $product = $machine_allocation->product;

        // استخراج BOMکالا از روی گروه ماشین، ماشین جدید
        $line_product_station = LineProductStation::where([
            "product_id" => $product->id,
            "machine_type_id" => $production_channel->machine->machine_type_id
        ])->first();
        if (!$line_product_station) {
            return [
                "result" => false,
                "error" => "هیچ مسیرم محصولی برای  " . $production_channel->machine->machine_type->caption . " یافت نشد."
            ];
        }

        $bom = BOM::
        where([
            "product_id" => $product->id,
            "product_route_id" => $line_product_station->product_route_id ?? 0
        ])->first();
        if (!$bom) {
            return [
                "result" => false,
                "error" => "هیچ BOMی برای مسیر  " . $line_product_station->route->caption . " یافت نشد."
            ];
        }


        $bom_items = BOMItem::
        where([
            "product_id" => $product->id,
            "bill_of_material_id" => $bom->id ?? 0
        ])->
        select("bill_of_material_item.*")->
        get();
        if (count($bom_items) == 0) {
            return [
                "result" => false,
                "error" => "هیچ آیتم BOMی برای مسیر  " . $line_product_station->route->caption . " یافت نشد."
            ];
        }

        $current_machine_input_material_ids =
            CurrentMachineInput::
            where("allocation_id", $allocation->id)->
            pluck("material_id", "material_id")->
            toArray();

        // موجودی اعلام شده را به تابع بررسی حالت های تخصیص ارسال می کنیم.
        $declaredInventory = [];

        // شناسه کالاهایی که کالای جایگزین دارند و کالای جایگزین آنها انتخاب شده است.
        $bom_declared_inventory_material_ids = BOMReplace::
        where([
            "product_id" => $product->id,
            "bill_of_material_id" => $bom->id
        ])->
        whereIn("replace_product_id", $current_machine_input_material_ids)->

        pluck("material_id", "material_id")->
        toArray();


        // شناسه همه جایگزین هایی که کالای اصلی آنها انتخاب شده است و یا یکی از جایگزین های آنها انتخاب شده است.
        $bom_declared_inventory_replace_product_ids = BOMReplace::
        where([
            "product_id" => $product->id,
            "bill_of_material_id" => $bom->id
        ])->
        whereIn("material_id",
            array_merge($bom_declared_inventory_material_ids, $current_machine_input_material_ids))->


        pluck("replace_product_id", "replace_product_id")->
        toArray();


        // لیست آیتم های BOM که موجودی ماده اولیه آنها را نباید محاسبه کنیم.

        $bom_declared_inventory = array_merge($bom_declared_inventory_material_ids, $bom_declared_inventory_replace_product_ids, $current_machine_input_material_ids);
        foreach ($bom_declared_inventory as $material_id) {
            if (isset($current_machine_input_material_ids[$material_id])) {
                $declaredInventory[$material_id] = 99999999;// موجودی ماده اولیه کافی است.
            } else {
                $declaredInventory[$material_id] = 0; // موجودی صفر در نظر گرفته می شود.
            }
        }


        // حذف تخصیص های در انتظار تایید برای ماشین مقصد
        $allocation_delete_ids = MachineAllocation::where([
            "machine_id" => $production_channel->machine->id,
            "status_id" => 5310005
        ])->pluck("allocation_id")->toArray();

        Allocation\AllocationData::whereIn("allocation_id", $allocation_delete_ids)->delete();

        MachineAllocation::where([
            "machine_id" => $production_channel->machine->id,
            "status_id" => 5310005
        ])->delete();


        return [
            "result" => true,
            "declaredInventory" => $declaredInventory
        ];

    }

    public static function UpdateAllocationStatus($allocation, $form = null, $user_id = null)
    {
        // این تابع برای بروز رسانی وضعیت تخصیص اسستفاده می شود و در تخصیص های کالای امانی،
        // اگر مشتری همه بسته بندی ها را ارسال کرده باشد، باید وضعیت آن خاتمه یافته شود.
        if ($allocation->order_id) {
            if ($form) {

            }
            $exist_not_sending = OrderPackingForm::
            where("order_id", $allocation->order_id)->
            where("status_id", "!=", 6070002)-> // ارسال شده
            exists();
            if (!$exist_not_sending) {
                // اگر همه بسته بندی ها ارسال شده است، تخصیص را خاتمه یافته می کند.
                $allocation->status_id = 5312102; // خاتمه یافته
                $allocation->save();
                foreach ($allocation->items as $machine_allocation) {
                    $machine_allocation->status_id = 5312102; // خاتمه یافته
                    $machine_allocation->save();

                    event(new ContractorLogEvent(null, 5312102, $machine_allocation->production, $machine_allocation, "", $user_id, $allocation->order));

                }

                // اگر همه تخصیص ها خاتمه یافته شده بود، کارت ار هم خاتمه یافته می کند.
                if ($machine_allocation) {
                    $all_not_terminate_count = MachineAllocation::where("production_id", $machine_allocation->production_id)->
                    where("status_id", "!=", 5312102)-> // خاتمه یافته
                    count();
                    if ($all_not_terminate_count == 0) {
                        // اگر همه تخصیص ها خاتمه یافته شده بود، کارت را هم خاتمه یافته می کنیم.
                        $machine_allocation->production->waiting_status_id = 7011003; // خاتمه یافته
                        $machine_allocation->production->status_id = 520; // خاتمه یافته
                        $machine_allocation->production->save();
                        event(new ProductionCardLogEvent($machine_allocation->production, "", $user_id));
                    }
                }
            }
        }
    }
}



//for ($p = 2; $p <= $max_priority_number; $p++) {
//
//    $priority_number = $p - 1;
//
//    $materials_info = $end_result[$priority_number - 1]["material"];
//
//
//    // در هر مرحله مقدار باقی مانده را از حداقل مقدار قابل تولید کم می کنیم.
//    $remaining_allocation_amount -= $end_result[$priority_number - 1]["amount_can_be_produced"];
//
//    $amount_can_be_produced = $end_result[$priority_number - 1]["amount_can_be_produced"];
//
//    /******************************************************************/
//    /******************************************************************/
//    /******************************************************************/
//    // مقدار موجودی فعال را کم می کنیم،
//    // ممکن است یک کالا در بیش از یک ورودی وجود داشته باشد، بنابراین اول کل مصرف هر کالا را به دست می آوریم و
//    // سپس موجودی فعال تمام ورودی ها را بروز می کنیم.
//    $product_active_inventory_used = [];
//    foreach ($end_result[$priority_number - 1]["material"] as $material) {
//        if (!isset($product_active_inventory_used[$material["material_id"]])) {
//            $product_active_inventory_used[$material["material_id"]] = 0;
//        }
//        $product_active_inventory_used[$material["material_id"]] += $material["amount_required"] * $end_result[$priority_number - 1]["amount_can_be_produced"];
//    }
//
//    foreach ($current_machine_input_list as $current_machine_input) {
//        for ($priority_number_for_update = 1; $priority_number_for_update <= $max_priority_number; $priority_number_for_update++) {
//
//            $material_id = $material_priority[$current_machine_input->id][$priority_number_for_update]["material_id"];
//            // ممکن است یک کالا در ورودی های دیگر استفاده نشده باشد
//            if (isset($product_active_inventory_used[$material_id])) {
//
//                $material_priority[$current_machine_input->id][$priority_number_for_update]["active_inventory"] -= $product_active_inventory_used[$material_id];
//                if ($material_priority[$current_machine_input->id][$priority_number_for_update]["active_inventory"] < 0) {
//                    $material_priority[$current_machine_input->id][$priority_number_for_update]["active_inventory"] = 0;
//                }
//            }
//
//            // برای آنهایی که هنوز اولویت نگرفتند، مقدار قابل تولید را بروز می کنیم.
//            if (!isset($material_priority[$current_machine_input->id][$priority_number_for_update]["priority_number"])) {
//                $material_priority[$current_machine_input->id][$priority_number_for_update]["amount_for_production"] =
//                    max(
//                        round($material_priority[$current_machine_input->id][$priority_number_for_update]["active_inventory"]
//                            /
//                            self::GetAllRequiredAmountFromMaterialPriority(
//                                $material_priority,
//                                $priority_number_for_update,
//                                $material_id
//                            )
//                            , 2
//                        ),
//                        0
//                    );
//            }
//        }
//
//    }
//
//    /***************************************************************/
//    /***************************************************************/
//    /***************************************************************/
//
//    if ($remaining_allocation_amount > 0) {
//
//        $end_result[$priority_number] = Allocation::getMaxAllocationAmountAccordingToWarehouseByPriorityNumber(
//            $amount_can_be_produced,
//            $materials_info,
//            $material_priority,
//            $priority_number
//        );
//
//        // چک کردن اینکه این کالا جزء کالاهای مجاز تولید است یا خیر
//        $result_check_end_result = self::CheckForEndResult($end_result[$priority_number], $permutation_material_id_list);
//
//        if (!$result_check_end_result) {
//            $end_result[$priority_number]["amount_can_be_produced"] = 0;
//            $end_result[$priority_number]["not_allowed_for_production"] = 1;
//        }
//
//
//    } else {
//        $p = $max_priority_number + 1;
//    }
//
//}
