<?php

namespace App\Http\Controllers\LineProductStation\Machine;


use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\ProductionChannel\MachineProductionChannelType;
use App\Models\LineProduct\Machine\ProductionChannel\MachineTypeProductionChannelType;
use App\Models\Production\ProductionChannelType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class MachineProductionChannelTypeController extends Controller
{
    public function index(Machine $machine)
    {
        $production_channel_type_ids = MachineProductionChannelType::where('machine_id', $machine->id)->pluck("production_channel_type_id", "production_channel_type_id");
        $list_machine_type = MachineTypeProductionChannelType::where('machine_type_id', $machine->machine_type_id)->get();
        return view(
            'line_product_station.machine.machine_production_channel_type.index',
            compact('machine', 'production_channel_type_ids', "list_machine_type")
        );
    }

    public function store(Request $request, Machine $machine)
    {
        $list_machine_type = MachineTypeProductionChannelType::where('machine_type_id', $machine->machine_type_id)->get();

        MachineProductionChannelType::where("machine_id", $machine->id)->delete();
        if ($request->production_channel_type) {
            foreach ($request->production_channel_type as $production_channel_type_id => $val) {

                MachineProductionChannelType::create([
                    'machine_id' => $machine->id,
                    'production_channel_type_id' => $production_channel_type_id,
                    "machine_type_id"=>$machine->machine_type_id
                ]);
            }
        }
        return redirect()
            ->route('line_product_station.machine.production_channel_type.index', $machine)
            ->with('success', 'کانال تولید های ماشین با موفقیت بروز رسانی گردید.');
    }


}
