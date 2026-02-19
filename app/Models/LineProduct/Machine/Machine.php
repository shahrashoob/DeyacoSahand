<?php

namespace App\Models\LineProduct\Machine;


use App\Models\LineProduct\Machine\Maintenance\Maintenance;
use App\Models\LineProduct\Machine\ProductionChannel\MachineProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Station;
use App\Models\Production\Production;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Machine extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "caption",
        "number_code",
        "machine_count",
        "check_inventory_for_allocation",
        'checking_form_not_delivered_at_register_production',
        'next_relation_machine_code',
    ];

    public static function ExistsCode($code, $id = false)
    {
        if ($id) {
            return Machine::where("code", $code)->where("id", "!=", $id)->exists();
        }

        return Machine::where("code", $code)->exists();
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function on_status()
    {
        return $this->belongsTo(Status::class, "on_status_id");
    }

    public function production_status()
    {
        return $this->belongsTo(Status::class, "production_status_id");
    }

    public function maintenance_status()
    {
        return $this->belongsTo(Status::class, "maintenance_status_id");
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function machine_type()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function machine_off_reason()
    {
        return $this->belongsTo(MachineOffReason::class);
    }


    public function properties()
    {
        return $this->hasMany(MachineProperty::class, "machine_id", "id");
    }

    public function machineProductionChannelTypes()
{
    return $this->hasMany(MachineProductionChannelType::class, 'machine_id');
}

    public function get_maintenance_status()
    {
        if ($this->maintenance_status_id == 6002001) {
            return $this->maintenance_status->caption;
        }
        $maintatence = Maintenance::where("machine_id", $this->id)->
        where("status_id", "!=", 6003003)-> // خاتمه یافته
        first();

        if (isset($maintatence)) {
            return $maintatence->status->caption . " (" . $maintatence->maintenance_type->caption . ")";
        }

// نباید اینجا بیاد، اگر آمد مشکلی وجود دارد.
        return $this->maintenance->caption;
    }
//    public function current_production()
//    {
//       $ma= MachineAllocation::where(["machine_id"=>$this->id,"status_id"=>5310005])->orderByDesc("id")->first();
//        if(isset($ma)){
//            return $ma->production;
//        }
//        return null;
//    }
//    public function reserve_production()
//    {
//        $ma= MachineAllocation::where(["machine_id"=>$this->id,"status_id"=>5310005])->orderByDesc("id")->first();
//        if(isset($ma)){
//            return $ma->reserve_production;
//        }
//        return null;}

    public function getIC()
    {
        // تابغی همه درخواست دهنده های باید داشته باشند.
        return $this->machine_type->ic;
    }

    public function getCode()
    {

//        if ( $this->code != "" ) {
//            return $this->code;
//        }
        $code_number = Machine::where("machine_type_id", $this->machine_type_id)->where("id", "<", $this->id)->count() + 1;
        $code = $string = Str::of($code_number)
            ->when($code_number < 100, function ($string) {
                return Str::of('0')->append($string);
            })->when($code_number < 10, function ($string) {
                return Str::of('0')->append($string);
            });
        $this->code = $this->machine_type->code . "" . $code;
        $this->save();

        return $this->code;
    }

    public function fullCaption()
    {
        return $this->code . " - " . $this->caption;
    }

    public function getOperator($type)
    {
        $machine_log = MachineLog::where("machine_id", $this->id)->orderByDesc("id")->first();

        if ($machine_log && $machine_log->operator) {
            switch ($type) {
                case "fullname":
                    return $machine_log->operator->fullname();
                    break;
            }
        }

    }

    public function getProduct()
    {
        if (isset($this->current_production)) {
            return $this->current_production->product;
        } else {
            return $this->reserve_production->product;
        }
    }

    // Production Channel
    public function getCurrentProductionChannel()
    {
        return ProductionChannel::where("machine_id", $this->id)->where("status_id", 3358001)->orderByDesc("priority_number")->first();
    }

    public function machine_production_channel_types()
    {
        return $this->belongsTo(MachineProductionChannelType::class,"id","machine_id");
    }
    public function getCurrentProductionChannelByStationOperation($station_operation_category_id)
    {
        return ProductionChannel::
        where([
            "machine_id" => $this->id,
            "status_id" => 3358001,
            "station_operation_category_id" => $station_operation_category_id
        ])->
        orderByDesc("priority_number")->
        first();

    }

    public function ReserveProductionChannel()
    {
        return ProductionChannel::where("machine_id", $this->id)->where("status_id", 3358002)->orderByDesc("priority_number")->get();
    }

    public function getReserveProductionChannelByStationOperation($station_operation_category_id)
    {
        return ProductionChannel::
        where([
            "machine_id" => $this->id,
            "status_id" => 3358002,
            "station_operation_category_id" => $station_operation_category_id
        ])->
        orderByDesc("priority_number")->get()
        ;

    }

    // Production Card
    public function getCurrentAllocation($number_skip = 0)
    {
        return Allocation::where("machine_id", $this->id)->where("status_id", 5310010)->orderByDesc("id")->skip($number_skip)->first();
    }

    public function ReserveAllocation($production_id = false, $production_type_id = false, $orderByPriority = "Asc", $allocation_illegal_ids = null,$min_priority_number = null)
    {
        $allocation_ids[] = -1;
        if ($production_type_id) {
            $allocation_ids = Allocation::join("machine_allocation", "allocations.id", "allocation_id")->
            join("production_cards", "production_id", "production_cards.id")->
            where("allocations.machine_id", $this->id)->
            where("allocations.status_id", 5310040)->
            where("production_cards.production_type_id", $production_type_id)->
            pluck("allocations.id")->toArray();
            $allocation_ids[] = -1;
        }

        return Allocation::
        where("machine_id", $this->id)->
        where("status_id", 5310040)->
        when($production_id, function ($query) use ($production_id) {
            // این شرط اشتباه است و هیچ وقت اجرا نمی شود.
            return $query->where("production_id", $production_id);
        })->
        when($production_type_id, function ($query) use ($allocation_ids) {
            return $query->whereIn("allocations.id", $allocation_ids);
        })->
        when($min_priority_number, function ($query) use ($min_priority_number) {
            return $query->where("priority_number",">=", $min_priority_number);
        })->

        when($orderByPriority == "Asc", function ($query) use ($allocation_ids) {
            return $query->orderBy("priority_number");// ابتدا بر اساس اولویت مرتب می شوند و بعد بر اساس شناسه جدول
        })->
        when($orderByPriority == "Desc", function ($query) use ($allocation_ids) {
            return $query->orderByDesc("priority_number");// ابتدا بر اساس اولویت مرتب می شوند و بعد بر اساس شناسه جدول
        })->
        when($allocation_illegal_ids, function ($query) use ($allocation_illegal_ids) {
            return $query->whereNotIn("allocations.id", $allocation_illegal_ids);
        })->

        orderBy("id");
    }

    public function getReserveAmount()
    {
        return MachineAllocation::
        where("machine_id", $this->id)->
        where("status_id", 5310040)->
        sum("allocation_amount");
    }

    public function getRemainingAmountOfCurrentAllocation()
    {
        $allocation = $this->getCurrentAllocation();
        $amount = 0;
        if ($allocation) {
            foreach ($allocation->items as $item) {
                $amount += ($item->max_number_of_doffs - $item->number_of_doffs_done) * $item->amount_of_each_doffs;
            }
        }

        return $amount;
    }

    public function getFirstReserveAllocation($production_type_id = false, $allocation_illegal_ids = null)
    {
        return $this->ReserveAllocation(false, $production_type_id, "Asc", $allocation_illegal_ids)->first();
    }

    // Production Form
    public function getCurrentProductionForm($checklist_code = "current_production_form_status")
    {

        $checklist = MachineModuleType::getChecklist($this->machine_type->machine_module_type_id, $checklist_code);

        return ProductionForm::        whereIn(
            "status_id",
            $checklist
        )->
        where("machine_id", $this->id)->
        orderByDesc("id")->
        first();
    }

    // Production Form
    public function getReserveProductionForm()
    {
        $checklist = MachineModuleType::getChecklist($this->machine_type->machine_module_type_id, "reserve_production_form_status");

        return ProductionForm::        whereIn(
            "status_id",
            $checklist
        )->
        where("machine_id", $this->id)->
        orderBy("id")->
        first();
    }


    public function setStatus($active_status_id, $on_status_id, $production_status_id, $machine_off_reason_id, $goods_kind_caption_en = "Fabric_Raw")
    {

        $goods_kind_caption_en = "حذف";
        if (!$this->machine_type->machine_module_type) {
            return [
                "result" => false,
                "message" => "نوع ماژول ماشین برای گروه های ماشین مشخص نشده است."
            ];
        }
        $no_order_status_id = $this->machine_type->machine_module_type->machine_production_status_id;
        $machine_off_reason = $this->machine_type->machine_module_type->machine_off_reason;

        if ($active_status_id == $no_order_status_id) { // غیرفعال
            if ($this->production_status_id != $this->machine_type->machine_module_type->machine_production_status_id) { // نداشتن سفارش
                return [
                    "result" => false,
                    "message" => "برای غیرفعال کردن ماشین باید وضعیت تولید ماشین 'نداشتن سفارش' باشد"
                ];
            }
            $this->active_status_id = 1210;
            $this->production_status_id = $no_order_status_id; //نداشتن سفارش
            $this->on_status_id = 53002;// خاموش
            $this->machine_off_reason_id = $machine_off_reason; // نداشتن برنامه (سفارش)
            $this->save();

            return [
                "result" => true
            ];

        }

        if ($on_status_id == 53002 && $machine_off_reason_id == null) {

            return [
                "result" => false,
                "message" => "علت خاموشی دستگاه مشخص نشده است."
            ];

        }
        if ($active_status_id != null) {
            $this->active_status_id = $active_status_id;
        }
        if ($production_status_id != null) {
            $this->production_status_id = $production_status_id;
        }
        if ($on_status_id != null) {
            $this->on_status_id = $on_status_id;
        }
        if ($machine_off_reason_id != null) {
            $this->machine_off_reason_id = $machine_off_reason_id;
        }
        if ($on_status_id == 53001) {
            $this->machine_off_reason_id = null;
        }
        $this->save();

        return [
            "result" => true
        ];


    }

}

