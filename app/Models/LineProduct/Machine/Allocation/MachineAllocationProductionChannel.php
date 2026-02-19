<?php

namespace App\Models\LineProduct\Machine\Allocation;

use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\Production\ProductionChannelType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocationProductionChannel extends Model
{
    use HasFactory;

    protected $table = "machine_allocation_production_channel";
    protected $fillable = [
        "allocation_id",
        "production_channel_id",
        "amount"
    ];

    public function allocation()
    {
        return $this->belongsTo(Allocation::class);
    }

    public function production_channel()
    {
        return $this->belongsTo(ProductionChannel::class);
    }

    public function datetime()
    {
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i Y/m/d ');

    }

    /**
     * @param \App\Models\LineProduct\Machine\Machine $machine
     * @param \App\Models\LineProduct\Machine\Allocation $allocation
     * @param \App\Models\Production\ProductionChannelType $production_channel_type
     * @param                                              $allocation_amount
     * ثبت مقدار تخصیص برای هر کانال
     *
     * @return array|bool[]
     */
    public static function AddAllocation(Machine $machine, Allocation $allocation, $production_channel_type_id, $station_operation_category_id, $allocation_amount)
    {

        // گرفتن اولین کانال تولید که با کانال کارت متفاوت است. و سپس  باید از آنجا به بعد تخصیص را به کانال ها اختصاص می دهد.
        $first_diff_production_channel = ProductionChannel::
        where("machine_id", $machine->id)->
        whereIn("status_id", [3358001, 3358002])->
        where("production_channel_type_id", "!=", $production_channel_type_id)->
        where("station_operation_category_id", $station_operation_category_id)->
        orderByDesc("id")->
        first();

        $current_or_reserve_production = ProductionChannel::
        where("machine_id", $machine->id)->
        whereIn("status_id", [3358001, 3358002])->
        where("production_channel_type_id", $production_channel_type_id)->
        where("station_operation_category_id", $station_operation_category_id)->
        where("remaining_capacity", ">", 0)->
        where("id", ">", $first_diff_production_channel->id ?? 0)->
        orderBy("id")->get();

        if (count($current_or_reserve_production) == 0) {
            return [
                "result" => false,
                "error" => "هیچ کانال تولیدی برای ماشین وجود ندارد که بتوان تخصیص شماره " . $allocation->id . " را به آن اضافه کرد. "
            ];
        }


        // به ازای یک واحد کالا، چقدر از کانال تولید مصرف می شود.
        $production_channel_consumption_amount = $allocation_amount * $allocation->consumption_percent_of_production_channel;

        $machine_allocation_production_list = [];
        foreach ($current_or_reserve_production as $production_channel) {
            $remaining_capacity = $production_channel->remaining_capacity;
            if ($production_channel_consumption_amount > $remaining_capacity) {
                $amount = $remaining_capacity;
                $production_channel_consumption_amount -= $remaining_capacity;
            } else {
                $amount = $production_channel_consumption_amount;
                $production_channel_consumption_amount = 0;
            }

            if ($remaining_capacity > 0) {
                $machine_allocation_production_list[] = [
                    "allocation_id" => $allocation->id,
                    "production_channel_id" => $production_channel->id,
                    "production_channel" => $production_channel,
                    "amount" => $amount
                ];
            }
        }

        if ($production_channel_consumption_amount != 0) {
            return [
                "result" => false,
                "error" => "ظرفیت کانال  های تولید جاری و رزرو برای  این تخصیص  "
                    . " کافی نمی باشد، لازم است تا یک کانال تولید جدید برای ماشین ایجاد شود." .
                    "<br/>" . " به ازای یک واحد کالا،  " .
                    $allocation->consumption_percent_of_production_channel . " واحد "
                    . " از ظرفیت کانال تولید مصرف می شود و نیاز از تا " .
                    $production_channel_consumption_amount
                    . " واحد از کانال تولید های جاری و رزرو باقی مانده باشد."
            ];
        }

        foreach ($machine_allocation_production_list as $item) {
            MachineAllocationProductionChannel::where([
                "allocation_id" => $item["allocation_id"],
                "production_channel_id" => $item["production_channel_id"],
            ])->delete();
            MachineAllocationProductionChannel::create($item);

            ProductionChannel::UpdateProductionChannel($item["production_channel"], null, $allocation->id);
        }


        return [
            "result" => true,
        ];
    }

    /**
     * @param \App\Models\LineProduct\Machine\Machine $machine
     * @param \App\Models\LineProduct\Machine\Allocation $allocation
     * @param                                            $new_allocation_amount
     * کاهش مقدار تخصیص
     *
     * @return array|bool[]
     */
    public static function ReduceAllocationAmount(Allocation $allocation, $new_allocation_amount)
    {

        $current_or_reserve_machine_allocation_production_channel = MachineAllocationProductionChannel::
        where("allocation_id", $allocation->id)->
        orderByDesc("id")-> // اول از آخرین کانال حذف می کند و سپس به اولین کانال می رسد
        get();

        if (count($current_or_reserve_machine_allocation_production_channel) == 0) {
            return [
                "result" => false,
                "error" => "هیچ کانال تولیدی برای  تخصیص شماره " . $allocation->id . " وجود ندارد. "
            ];
        }

        $old_allocation_amount = MachineAllocationProductionChannel::
        where("allocation_id", $allocation->id)->
        sum("amount");

        $reduce_amount = $old_allocation_amount - $new_allocation_amount; // این مقدار همیشه مثبت است، چون مقدار یک تخصیص را همیشه کم می کنیم و اضافه نمی کنیم.

        foreach ($current_or_reserve_machine_allocation_production_channel as $machine_allocation_production_channel) {
            if ($reduce_amount > 0) {
                if ($machine_allocation_production_channel->amount <= $reduce_amount) {
                    $amount = $machine_allocation_production_channel->amount;
                    $machine_allocation_production_channel->amount = 0;
                    $machine_allocation_production_channel->save();
                    $reduce_amount -= $amount;
                } else {

                    $amount = $reduce_amount;
                    $machine_allocation_production_channel->amount = $machine_allocation_production_channel->amount - $amount;
                    $machine_allocation_production_channel->save();
                    $reduce_amount = 0;
                }
                if (!$machine_allocation_production_channel->production_channel) {
                    return [
                        "result" => false,
                        "error" => " کانال تولید برای تخصیص با کد رهگیری " . $machine_allocation_production_channel->id . " نامعتبر است، لطبا با پشتیبانی تماس بگیرید."
                    ];
                }
                // بروز رسانی مقدار باقی مانده و اولویت کانال
                ProductionChannel::UpdateProductionChannel($machine_allocation_production_channel->production_channel, null, null, true);
            }
        }


        return [
            "result" => true,
        ];
    }
}
