<?php

namespace App\Http\Controllers\GoodsKindProcess\General\ProductionCard;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\Production\Production;
use App\Models\Production\ProductionChannelType;
use Illuminate\Http\Request;

class GeneralProductionChannelController extends Controller
{
    var $view_path = "goods_kind_process.general.production_card.production_channel.";
    var $route_path;
    var $dashboard_route;

    public function create_new_channel(Machine $machine, Production $production, $allocation_amount)
    {

// انتخاب اولین مسیر محصول، حتما هم وجود دارد
        $line_product_station = LineProductStation::where([
            "product_id" => $production->product_id,
            "machine_type_id" => $machine->machine_type_id
        ])->first();

        $result_production_chanel = ProductionChannel::CheckProductionChannelForAllocation(
            $machine, $production, $allocation_amount,
            $line_product_station->station_operation_id,
            $line_product_station->station_operation->station_operation_category_id
        );

        if (!isset($result_production_chanel["warning"]) || !isset($result_production_chanel["production_channel_type"])) {
            return back()->withErrors("نیازی به تعریف کانال نمی باشد، لطفا یک بار دیگر تلاش کنید.");
        }

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        $message = $result_production_chanel["warning"];
        $production_chanel = $result_production_chanel["production_channel"];
        $production_channel_type = $result_production_chanel["production_channel_type"];
        $machine_type_production_channel_type = MachineTypeProductionChannelType::
        where([
            "machine_type_id" => $machine->machine_type_id,
            "production_channel_type_id" => $production_channel_type->id
        ])->first();

        if (!$machine_type_production_channel_type) {
            return back()->withErrors("اطلاعات کانال تولید در گروه ماشین به درستی ثبت نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }

        return view($this->view_path . "create_new_channel", compact(
            "machine", "production", "route_path", "dashboard_route",
            "message", "production_chanel", "production_channel_type", "machine_type_production_channel_type"));
    }

    public function store_new_channel(Request $request, Machine $machine, Production $production, $station_operation_category_id)
    {

        $machine_type_production_channel_type = MachineTypeProductionChannelType::
        where([
            "machine_type_id" => $machine->machine_type_id,
            "production_channel_type_id" => $request->production_channel_type_id
        ])->first();

        if (!$machine_type_production_channel_type) {
            return back()->withErrors("اطلاعات کانال تولید در گروه ماشین به درستی ثبت نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }

        $capacity = $request->capacity;
        if ($capacity > $machine_type_production_channel_type->production_channel_type->max_capacity || $capacity < $machine_type_production_channel_type->production_channel_type->min_capacity) {
            return back()->withErrors("ظرفیت کانال تولید به درستی وارد نشده است.");
        }

        $result_production_channel = $production_channel = self:: create_production_channel($machine, ProductionChannelType::find($request->production_channel_type_id),
            $machine_type_production_channel_type->production_channel_type->min_capacity,
            $machine_type_production_channel_type->production_channel_type->max_capacity, true,
            $station_operation_category_id
        );
        if (!$result_production_channel["result"]) {
            return back()->withErrors($result_production_channel["error"]);
        }


        return redirect()->route($this->dashboard_route . "index", $production)->with(["success" => "یک کانال تولید با کد " . $production_channel->getCode() . " برای ماشین ایجاد گردید."]);
    }

    public static function create_production_channel(Machine $machine, ProductionChannelType $production_channel_type, $min_capacity, $max_capacity, $allow_create_channel, $station_operation_category_id)
    {

        // چک کردن اینکه تعداد کانال مشابه پشت سر هم ایجاد شده بیش از حذ مجاز نباشند.
        $list = ProductionChannel::where([
            "machine_id" => $machine->id,
            "production_channel_type_id" => $production_channel_type->id,
        ])->
        where("status_id", [3358003, 3358004])-> // تولید شده و کنسل شده
        orderByDesc("id")->
        get();
        $number = 0;
        foreach ($list as $item) {
            if ($item->production_channel_type_id == $production_channel_type->id) {
                $number++;
            } else {
                break;
            }
        }

        if ($number + 1 > $production_channel_type->max_number_of_sequences) {
            return [
                "result" => false,
                "error" => " حداکثر تعداد متوالی کانال تولید " . $production_channel_type->caption . "، $number عدد می باشد." . " لطفا ماشین دیگری را انتخاب نمایید."
            ];
        }

        if (!$allow_create_channel) {
            return [
                "result" => true,
            ];
        }

        $production_channel = ProductionChannel::create([
            "machine_id" => $machine->id,
            "production_channel_type_id" => $production_channel_type->id,
            "status_id" => 3358002, // کانال رزرو
            "min_capacity" => $min_capacity,
            "max_capacity" => $max_capacity,
            "priority_number" => 9999999,
            "station_operation_category_id" => $station_operation_category_id
        ]);

        ProductionChannel::UpdatePriorityNumber($machine, $production_channel);
        ProductionChannel::UpdateProductionChannel($production_channel);

        return [
            "result" => true,
            "production_channel" => $production_channel
        ];
    }

}
