<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Station;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use Illuminate\Http\Request;

class MachineStatusController extends Controller {

    private $view_path = "line_product_station.machine_status.";
    private $route_path = "line_product_station.machine_status.";

    public function index( MachineType $machine_type, MachineModuleType $machine_module_type ) {

        return view( $this->view_path . "index", compact( "machine_type", "machine_module_type" ) );
    }


    public function update_possibility_allocation( Request $request,  MachineModuleType $machine_module_type ) {
        if ( ! isset( $request->data["production_status_id"] ) ) {
            return back()->withErrors( "لطفا حداقل یک وضعیت برای تخصیص کارت تولید انتخاب نمایید" );
        }
        if ( ! isset( $request->data["extraction_status_id"] ) ) {
            return back()->withErrors( "لطفا حداقل یک وضعیت برای استخراج فرم انتخاب نمایید" );
        }

        MachineStatus::where( [ "machine_module_type_id" => $machine_module_type->id ] )->
        update( [
            "possibility_of_allocation_machine"         => 0,
            "possibility_of_extraction_production_form" => 0
        ] );

        // بروز رسانی امکان تخصیص
        foreach ( $request->data["production_status_id"] as $key => $status_id ) {
            $machine_status = MachineStatus::where( [
                "machine_module_type_id"  => $machine_module_type->id,
                "production_status_id" => $key
            ] )->first();

            if ( isset( $machine_status ) ) {
                $machine_status->possibility_of_allocation_machine = true;
                $machine_status->save();
            }
        }

        // وضحیت امکان استخراج فرم تولید
        foreach ( $request->data["extraction_status_id"] as $key => $status_id ) {
            $machine_status = MachineStatus::where( [
                "machine_module_type_id"  => $machine_module_type->id,
                "production_status_id" => $key
            ] )->first();

            if ( isset( $machine_status ) ) {
                $machine_status->possibility_of_extraction_production_form = true;
                $machine_status->save();
            }
        }


        // حداکثر زمان مجاز توقف
        foreach ( $request->data["max_stop_allowed"] as $key => $max_stop_allowed ) {
            $machine_status = MachineStatus::where( [
                "machine_module_type_id"  => $machine_module_type->id,
                "production_status_id" => $key
            ] )->first();

            if ( isset( $machine_status ) ) {
                $machine_status->max_stop_allowed = $max_stop_allowed;
                $machine_status->save();
            }
        }

        return back()->with( [ "success" => "بروزرسانی با موفقیت انجام شد" ] );
    }
}
