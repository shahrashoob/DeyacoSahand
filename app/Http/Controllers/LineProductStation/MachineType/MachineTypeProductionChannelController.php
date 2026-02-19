<?php

namespace App\Http\Controllers\LineProductStation\MachineType;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKind\GoodsKindClassification;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeMachineFault;
use App\Models\LineProduct\Machine\MachineTypeOutputBand;
use App\Models\LineProduct\Machine\ProductionChannel\MachineProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\ProductionChannel;
use App\Models\LineProduct\Product\Fault\ProductFault;
use App\Models\LineProduct\Product\Fault\ProductFaultProductFaultSign;
use App\Models\LineProduct\Product\Fault\ProductFaultSign;
use App\Models\Production\ProductionChannelNextOne;
use App\Models\Production\ProductionChannelType;
use App\Models\Utility\Option;
use App\Http\Controllers\LineProductStation\ProductionChannelType\DefinitionController;
use Illuminate\Http\Request;

class MachineTypeProductionChannelController extends Controller
{
    private $view_path = "line_product_station.machine_type.production_channel.";
    private $route_path = "line_product_station.machine_type.production_channel.";

    public function index(MachineType $machine_type)
    {

        $production_channel_type_option = Option::get("station_production_channel_type", 0, $machine_type->station_id);

        return view($this->view_path . "index", compact("machine_type", "production_channel_type_option"));
    }

    public function store(Request $request, MachineType $machine_type)
    {

        $production_channel_type = ProductionChannelType::find($request->production_channel_type_id);
        if (!$production_channel_type) {
            return back()->withErrors("نوع کانال معتبر نمی باشد.");
        }

        $exists = MachineTypeProductionChannelType::where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $request->production_channel_type_id
        ])->exists();

        if ($exists) {
            return back()->withErrors("این نوع کانال قبلا به گروه ماشین اضافه شده است.");
        }
        MachineTypeProductionChannelType:: create([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $request->production_channel_type_id
        ]);

        return redirect()->back()->with(["success" => "نوع کانال با موفقیت اضافه شد"]);
    }

    public function delete(MachineType $machine_type, ProductionChannelType $production_channel_type)
    {

        if (ProductionChannelNextOne::where("machine_type_id", $machine_type->id)->count() > 0) {
            return back()->withErrors("لطفا قبل از حذف کانال، لیست کانال های مجاز بعدی را حذف کنید.");
        }

        if (MachineProductionChannelType::join("machines", "machines.id", "machine_id")->
            where("machine_type_id", $machine_type->id)->count() > 0) {
            return back()->withErrors("با توجه به اینکه این کانال بر روی  تعدادی از ماشین های این گروه ماشین، مجاز می باشد، ابتدا نسبت به غیر مجاز نمودن آن اقدام نمایید. ");
        }
        MachineTypeProductionChannelType:: where([
            "machine_type_id" => $machine_type->id,
            "production_channel_type_id" => $production_channel_type->id
        ])->delete();

        return redirect()->back()->with(["success" => "نوع کانال با موفقیت حذف شد"]);
    }

    public function edit_next_ones(MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        return DefinitionController::EditNextNone($machine_type, $production_channel_type, $this->view_path);
    }

    public function update_next_ones(Request $request, MachineType $machine_type, ProductionChannelType $production_channel_type)
    {
        return DefinitionController::UpdateNextOne($request, $machine_type, $production_channel_type);
    }

    public function delete_next_ones(MachineType $machine_type, ProductionChannelType $production_channel_type, $next_production_channel_type_id)
    {
        return DefinitionController::DeleteNextOne($machine_type, $production_channel_type, $next_production_channel_type_id);
    }


}
