<?php

namespace App\Models\LineProduct\Machine\ProductionChannel;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationProductionChannel;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Station\Operation\StationOperationCategory;
use App\Models\LineProduct\StationOperation;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelNextOne;
use App\Models\Production\ProductionChannelType;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionChannel extends Model
{
    use HasFactory;

    protected $table = "production_channels";
    protected $fillable = [
        "machine_id",
        "production_channel_type_id",
        "station_operation_category_id",
        "status_id",
        "max_capacity",
        "min_capacity",
        "priority_number"
    ];

    public function production_channel_type()
    {
        return $this->belongsTo(ProductionChannelType::class);
    }

    public function station_operation_category()
    {
        return $this->belongsTo(StationOperationCategory::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function machine()
    {
        return $this->belongsTo(Machine::class);
    }

    public function create_datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    public function getCode()
    {
        return "DCPC/" . (1000 + $this->id);
    }

    public static function UpdateProductionChannel(ProductionChannel $production_channel, $new_capacity = null, $allocation_id = null, $remove_reserve_amount_zero = false)
    {

        if ($new_capacity) {
            $production_channel->max_capacity = $new_capacity;
            $production_channel->min_capacity = $new_capacity;
        }

        $current_allocation_amount = 0;
        if ($allocation_id) {
            $current_allocation_amount = MachineAllocationProductionChannel::
            where("allocation_id", $allocation_id)->
            where("production_channel_id", $production_channel->id)->
            sum("machine_allocation_production_channel.amount");
        }

        $reserve_amount = $current_allocation_amount +
            MachineAllocationProductionChannel::
            join("allocations", "allocations.id", "allocation_id")->
            whereIn("allocations.status_id", [5310010, 5310020, 5310040])->
            where("production_channel_id", $production_channel->id)->
            sum("machine_allocation_production_channel.amount");

        $production_channel->remaining_capacity = round($production_channel->max_capacity - $reserve_amount, 6);;
        $production_channel->save();

        // کانال تولید هایی که رزرو هستند و از ظرفیت آنها هیچ استفاده ای نشده است، را حذف می کنیم.
        if ($remove_reserve_amount_zero && $production_channel->status_id == 3358002 && $reserve_amount <= 0) {
            $production_channel->delete();
        }


    }

    public static function CheckProductionChannelForAllocation(Machine $machine, Production $production, $allocation_amount, $station_operation_id, $station_operation_category_id, $production_channel_type = null)
    {

        if (!$production_channel_type) {
            $production_channel_type = $production->getProductionChannelType();
            if (!$production_channel_type) {
                return [
                    "result" => false,
                    "error" => "نوع کانال تولید برای کارت تولید " . $production->serial() . " نامعتبر است."
                ];
            }
        }

        // اگر کانال رزرو نداشت، کانال جاری را در نظر می گیریم.
        $production_channel_reserve = $machine->getReserveProductionChannelByStationOperation($station_operation_category_id)->first();
        if ($production_channel_reserve) {
            $production_channel = $production_channel_reserve;
        } else {
            $production_channel = $machine->getCurrentProductionChannelByStationOperation($station_operation_category_id);
            if (!$production_channel) {
                return [
                    "result" => false,
                    "warning" => "کانال تولید جاری برای ماشین وجود ندارد.",
                    "production_channel" => null,
                    "production_channel_type" => $production_channel_type,
                    "station_operation_category_id" => $station_operation_category_id

                ];
            }
        }

        // چک کردن اینکه کانال تولید بعدی معتبر است یا خیر
        if ($production_channel->production_channel_type_id != $production_channel_type->id) {
            // اگر نوع کانال تولید ها متفاوت است، اقدام به بررسی کانال بعدی مجاز می کند.
            $next_one_exists = ProductionChannelNextOne::where([
                "machine_type_id" => $machine->machine_type_id,
                "production_channel_type_id" => $production_channel->production_channel_type_id,
                "next_production_channel_type_id" => $production_channel_type->id,
            ])->exists();
            if (!$next_one_exists) {
                $station_operation = StationOperation::find($station_operation_id);

                $message =
                    "با توجه اینکه آخرین کانال تولید ماشین از دسته عملیات (" . $station_operation->caption . " در گروه ماشین " . $machine->machine_type->caption .
                    ")، " . $production_channel->production_channel_type->caption . " می باشد و برای رزور کارت جدید،
                 نیاز است تا کانال تولید از نوع " . $production_channel_type->caption . " برای ماشین تعریف گردد." .
                    "<br/>" . " لذا با توجه به مشخصات کانال تولید (" . $production_channel->production_channel_type->caption .
                    ") امکان تعریف کانال جدید از نوع " . $production_channel_type->caption . " بعد از آن وجود ندارد.";
                return [
                    "result" => false,
                    "error" => $message,
                ];

            }
        }


        if ($production_channel_type->id != $production_channel->production_channel_type_id) {
            return [
                "result" => false,
                "warning" => "کانال تولید ماشین و کانال تولید کارت تولید با هم برابر نیستند",
                "production_channel" => $production_channel,
                "production_channel_type" => $production_channel_type,
                "station_operation_category_id" => $station_operation_category_id
            ];
        }


        $remaining_capacity = ProductionChannel::where("machine_id", $machine->id)->
        where("production_channel_type_id", $production_channel_type->id)->
        whereIn("status_id", [3358002, 3358001])->sum("remaining_capacity");


        if ($remaining_capacity < $allocation_amount) {
            return [
                "result" => false,
                "warning" => "ظرفیت کانال تولید کافی نمی باشد",
                "production_channel" => $production_channel,
                "production_channel_type" => $production_channel_type,
                "station_operation_category_id" => $station_operation_category_id
            ];
        }

        return [
            "result" => true,
            "production_channel_type" => $production_channel_type,
            "station_operation_category_id" => $station_operation_category_id
        ];
    }

    public static function ChangeChannel(Allocation $allocation)
    {

        $machine_allocation_production_channel = Allocation\MachineAllocationProductionChannel::
        where("allocation_id", $allocation->id)->
        orderBy("id")->first();
        if ($machine_allocation_production_channel) {

            // کانال جاری قبلی را به تولید شده تغییر می دهد.
            ProductionChannel::
            where("machine_id", $allocation->machine_id)->
            where("status_id", 3358001)->update(["status_id" => 3358003]);

            // اولین کانالی که تخصیص در آن قرار دارد را جاری می کند.
            $machine_allocation_production_channel->production_channel->status_id = 3358001;
            $machine_allocation_production_channel->production_channel->save();


        }
    }

    public static function UpdatePriorityNumber(Machine $machine, $production_channel = null)
    {
        if ($production_channel) {
            $production_channel->priority_number =
                ProductionChannel::
                where("machine_id", $machine->id)->
                where("status_id", 3358002)->
                count();
            $production_channel->save();

            return 1;
        }
        // اولویت کانال های تولید شده 0 است.
        ProductionChannel::where([
            "machine_id" => $machine->id,
            "status_id" => 3358003
        ])->update(["priority_number" => 0]);

        // اولویت کانال جاری 1 است.
        ProductionChannel::where([
            "machine_id" => $machine->id,
            "status_id" => 3358001
        ])->update(["priority_number" => 1]);

        // اولویت کانال های روزور به ترتیب از 2 شروع می شود.
        ProductionChannel::where([
            "machine_id" => $machine->id,
            "status_id" => 3358002
        ])->
        orderBy("priority_number")->
        orderBy("id")->

        get();


    }

    /**
     * @param \App\Models\LineProduct\Machine\Machine $machine
     * @param \App\Models\Production\Production $production
     * @param                                         $amount
     * امکان سنجی انتقال x واحد از کارت تولید از ماشین m به یکی دیگر از ماشین های هم کانال
     *
     * @return array
     */
    public static function GetProductionChannelForTransferToAnotherMachine(Production $production, $amount, $machine_illegal_ids = [-1])
    {

        $production_channel_type = $production->getProductionChannelType();
        if (!$production_channel_type) {
            return [
                "result" => false,
                "error" => "نوع کانال تولید برای کارت تولید " . $production->serial() . " نامعتبر است."
            ];
        }
        // ابتدا کارتهای جاری را بررسی می کنیم.
        $production_channel = ProductionChannel::
        whereNotIn("machine_id", $machine_illegal_ids)-> // ماشین های غیر مجاز
        where("production_channel_type_id", $production_channel_type->id)->
        where("status_id", 3358001)->
//        where( "remaining_capacity", ">=", $amount )->
        first();

        if ($production_channel) {
            // اگر کانال جاری اوکی بود، نباید ماشین عیب فعال داشته باشد.
            $result_fault = Allocation::CheckAllocationFault($production_channel->machine, $production);
            if (!$result_fault["result"]) {
                $machine_illegal_ids[] = $production_channel->mahcine_id;

                return self:: GetProductionChannelForTransferToAnotherMachine($production, $amount, $machine_illegal_ids);

            }
        } // بررسی کارت های رزور به ترتیب اولویت اول و دوم...
        else {

            $max_priority_number = ProductionChannel::
                whereNotIn("machine_id", $machine_illegal_ids)-> // ماشین های غیر مجاز
                where("production_channel_type_id", $production_channel_type->id)->
                where("status_id", 3358002)->
                max("priority_number") + 0;

            for ($priority_number = 1; $priority_number <= $max_priority_number; $priority_number++) {

                $production_channel = ProductionChannel::
                whereNotIn("machine_id", $machine_illegal_ids)-> // ماشین های غیر مجاز
                where("production_channel_type_id", $production_channel_type->id)->
                where("status_id", 3358002)->
                where("priority_number", $priority_number)->
//                where( "remaining_capacity", ">=", $amount )->
                first();
                if ($production_channel) {
                    $result_fault = Allocation::CheckAllocationFault($production_channel->machine, $production);
                    if (!$result_fault["result"]) {
                        $machine_illegal_ids[] = $production_channel->mahcine_id;

                        return self:: GetProductionChannelForTransferToAnotherMachine($production, $amount, $machine_illegal_ids);

                    }
                    break;
                }
            }
        }

        if (!$production_channel) {
            return [
                "result" => false,
                "error" => "هیچ کانال تولیدی با ظرفیت خالی برای تخصیص جدید یافت نشد."
            ];

        }

        return [
            "result" => true,
            "production_channel" => $production_channel
        ];
    }


}
