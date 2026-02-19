<?php

namespace App\Models\Warehouse;

use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Allocation\Modification\MachineAllocationModificationForm;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Packing\PackingTypeLabelPrintingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Station;
use App\Models\Post\PostUser;
use App\Models\Utility\Financial\FinancialSoftwareTransKind;
use App\Models\Utility\Holdding\Factory;
use App\Models\HR\Shift\Shift;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "code",
        "caption",
        "ic",
        "warehouse_type_id",
        "belonging_to_id",
        "shift_id",
        "cheek_loading_control_for_exist_form",
        'allow_entry_with_pin',
        "exit_form_label_printing_type_ids",
        "earlier_delivery_time",
        "max_delivery_time",
        "allow_select_partial_of_packing_in_output",        
        "check_amount_product_in_entry"
    ];
    /**
     * @return int[]
     * قالب های مجاز جهت پرینت برگ خروج از انبار
     */
    public static  $PackingTypeLabelPrintingTypesValidExitFormIds =
        [
            1, 3, 4, 8, 9,1014
        ];

    public function warehouse_type()
    {
        return $this->belongsTo(WarehouseType::class);
    }

    public function get_exit_form_label_printing_type()
    {
        $exit_form_label_printing_type_ids = json_decode($this->exit_form_label_printing_type_ids);
        $exit_form_label_printing_type_ids[] = -1;
        return PackingTypeLabelPrintingType::whereIn('id', $exit_form_label_printing_type_ids)->get();
    }


    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function fullCaption()
    {
        return $this->code . " - " . $this->caption;
    }

    public function factory()
    {
        return $this->belongsTo(Factory::class);
    }

    public function GetBelongingToObject()
    {
        switch ($this->warehouse_type_id) {
            case 2:
                return Machine::find($this->belonging_to_id);
            case 3:
                return MachineType::find($this->belonging_to_id);
            case 4:
                return Station::find($this->belonging_to_id);
            case 5:
                return Line::find($this->belonging_to_id);
        }

        return null;
    }

    public function financial_software_trans_kind()
    {
        return $this->hasMany(FinancialSoftwareTransKind::class);
    }

    public static function ExistsCode($code, $id = false)
    {
        if ($id) {
            return Warehouse::where("code", $code)->where("id", "!=", $id)->exists();
        }

        return Warehouse::where("code", $code)->exists();
    }


    public static function getAllowedWarehouse()
    {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $warehouse_list = PostWarehouse::whereIn("post_id", $post_ids)->pluck("warehouse_id")->toArray();

        $warehouse_list[] = 0;

        return $warehouse_list;
    }

    public static function SetMachineWarehouse($object, $warehouse_type_id, $number = 1)
    {
        switch ($warehouse_type_id) {
            case 2:
                $machine = $object;
                if (!$machine->warehouse) {
                    $warehouse = Warehouse::create([
                        "code" => "M" . $machine->getCode(),
                        "caption" => "انبارک " . $machine->caption,
                        "warehouse_type_id" => 2,
                        "belonging_to_id" => $machine->id
                    ]);
                    $machine->warehouse_id = $warehouse->id;
                    $machine->save();
                } else {
                    $machine->warehouse->caption = "انبارک " . $machine->caption;
                    $machine->warehouse->save();
                }
                break;
            case 3:
                $machine_type = $object;
                $n = Warehouse::where([
                        "warehouse_type_id" => 3,
                        "belonging_to_id" => $machine_type->id
                    ])->count() + 1;
                for ($k = $n; $k < $number + $n; $k++) {

                    $warehouse = Warehouse::create([
                        "code" => "MT" . $machine_type->getCode() . $k,
                        "caption" => "انبارک " . $k . " " . $machine_type->caption,
                        "warehouse_type_id" => 3,
                        "belonging_to_id" => $machine_type->id
                    ]);
                }
                break;
            case 4:
                $station = $object;
                $n = Warehouse::where([
                        "warehouse_type_id" => 4,
                        "belonging_to_id" => $station->id
                    ])->count() + 1;
                for ($k = $n; $k < $number + $n; $k++) {
                    $warehouse = Warehouse::create([
                        "code" => "S" . $station->getCode() . $k,
                        "caption" => "انبارک " . $k . " " . $station->caption,
                        "warehouse_type_id" => 4,
                        "belonging_to_id" => $station->id
                    ]);
                }
                break;
            case 5:
                $line = $object;
                $n = Warehouse::where(["warehouse_type_id" => 5, "belonging_to_id" => $line->id])->count() + 1;
                for ($k = $n; $k < $number + $n; $k++) {
                    $warehouse = Warehouse::create([
                        "code" => "L" . $line->getCode() . $k,
                        "caption" => "انبارک " . $k . " " . $line->caption,
                        "warehouse_type_id" => 5,
                        "belonging_to_id" => $line->id
                    ]);
                }

                break;
        }

    }


    public static function warehouse_handling_need(Warehouse $warehouse, $product_id = null, $warehouse_limit = [], $machine_type_id = null)
    {
        $all_product_ids = PackingForm::join("packing_form_item", "packing_form_id", "packing_forms.id")->
        join("products", "products.id", "product_id")->
        where("warehouse_id", $warehouse->id)->
        where("warehouse_status_id", 4201)->
        when($product_id, function ($query) use ($product_id) {
            return $query->where("product_id", $product_id);
        })->
        groupBy("product_id")->
        pluck("goods_kind_id", "product_id")->
        toArray();

        $belonging_to_id = $warehouse->belonging_to_id;
        $warehouse_type_id = $warehouse->warehouse_type_id;

        // اگر انبارک ماشین بود، باید از محدودیت انبارک گروه ماشین برداشته شود.
        if ($machine_type_id) {
            $warehouse_type_id = 3;
            $belonging_to_id = $machine_type_id;
        }

        if ($warehouse->warehouse_type_id == 1) {
            return [
                "result" => false,
                "error" => "برای انبارهای اصلی نیاز به انبارگردانی نمی باشد."
            ];
        }

        $last_row_in_warehouse = WarehouseProduct::where([
            "warehouse_id" => $warehouse->id
        ])->
        when(count($all_product_ids) == 0, function ($query) {
            return $query->where("product_id", -1); // اگر هیچ کالایی نیست، بنابراین آرایه خالی برمی گرداند
        })->
        when(count($all_product_ids) > 0, function ($query) use ($all_product_ids) {
            return $query->whereIn("product_id", array_keys($all_product_ids));
        })->
        groupBy("product_id")->
        orderByDesc("created_at")->
        pluck("created_at", "product_id")->
        toArray();

        foreach ($all_product_ids as $product_id => $goods_kind_id) {
            if (isset($last_row_in_warehouse[$product_id])) {
                $last_update[$product_id] = $last_row_in_warehouse[$product_id];
            } else {
                $last_update[$product_id] = now();
            }
        }

        $last_modification = MachineAllocationModificationForm::
        join("machine_allocation_modifications", "machine_allocation_modifications.id", "machine_allocation_modification_id")->

        when(count($all_product_ids) == 0, function ($query) {
            return $query->where("product_id", -1); // اگر هیچ کالایی نیست، بنابراین آرایه خالی برمی گرداند
        })->
        when(count($all_product_ids) > 0, function ($query) use ($all_product_ids) {
            return $query->whereIn("product_id", array_keys($all_product_ids));
        })->
        where("warehouse_id", $warehouse->id)->
        where("machine_allocation_modifications.status_id", "!=", 6021001)-> // معلق
        groupBy("product_id")->
        selectRaw("max(machine_allocation_modifications.updated_at) as datetime, product_id")->
        pluck("datetime", "product_id")->
        toArray();

        foreach ($last_modification as $product_id => $datetime) {
            if (isset($last_row_in_warehouse[$product_id])) {
                $last_update[$product_id] = $datetime;
            }
        }

        $product_ids_warehouse_handling = [];
        $min = 1000000000;
        foreach ($all_product_ids as $product_id => $goods_kind_id) {
            $diff_days = Carbon::parse($last_update[$product_id])->diffInDays(Carbon::now());
//            return  "warehouse_limit[$warehouse_type_id][$goods_kind_id][$belonging_to_id]";
            if (!isset($warehouse_limit[$warehouse_type_id][$goods_kind_id][$belonging_to_id])) {
                $goods_kind = GoodsKind::find($goods_kind_id);
                return [
                    "result" => false,
                    "error" => " محدودیت انبارگردانی برای رسته کالایی " . ($goods_kind->caption ?? "---") . " در " . $warehouse->caption . " مشخص نشده است." .
                        "<br/>" . " محدودیت های زمانی برای خط تولید، ایستگاه کاری و گروه ماشین باید به صورت کامل ثبت گردد." .
                        "<br/>" . "برای انجام این کار لطفا با پشتیبانی تماس بگیرد."
                ];
            }
            if ($diff_days > $warehouse_limit[$warehouse_type_id][$goods_kind_id][$belonging_to_id]) {
                $product_ids_warehouse_handling[] = $product_id;

                if ($min > $warehouse_limit[$warehouse_type_id][$goods_kind_id][$belonging_to_id]) {
                    $min = $warehouse_limit[$warehouse_type_id][$goods_kind_id][$belonging_to_id];
                }
            }
        }

        if (count($product_ids_warehouse_handling) > 0) {
            return [
                "result" => true,
                "product_ids" => $product_ids_warehouse_handling,
                "warehouse_id" => $warehouse->id,
                "warehouse_handling_time_limit" => $min
            ];
        }

        return [
            "result" => false
        ];


    }
}
