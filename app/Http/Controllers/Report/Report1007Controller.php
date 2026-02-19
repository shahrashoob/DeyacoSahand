<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachinePropertyValue;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Production\ProductionForm;
use App\Models\Report\R1006\Report1006ProductionForm;
use App\Models\Utility\DateTime;
use Carbon\Carbon;
use Illuminate\Http\Request;

class Report1007Controller extends Controller {
    public function index() {
        return view( "report/1007/index" );
    }

    public function submit( Request $request ) {

        $start_date_time= DateTime::getDateTimeFromRequest($request,"start_date");
        $end_date_time= DateTime::getDateTimeFromRequest($request,"end_date");


        $machine_efficiency=Report1007Controller::getMachineEfficiency($start_date_time,$end_date_time);

        return view( "report.1007.show_report",compact("machine_efficiency"));
    }
    public static function getMachineEfficiency($start_date_time,$end_date_time){
        $start_machine_logs = MachineLog::
        where( "machine_logs.created_at", ">=", $start_date_time )->
        where( "machine_logs.created_at", "<=", $end_date_time )->
        whereNotNull( "contour_1_value" )->
        groupBy( "machine_id" )->
        selectRaw( "min(machine_logs.id) as machine_log_id,machine_id, min(contour_sum_value) as contour_sum_value,min(created_at) as created_at" )->
        get()->toArray();
        $start_machine_logs = collect( $start_machine_logs )->keyBy( 'machine_id' );

        $end_machine_logs = MachineLog::
        whereNotNull( "contour_1_value" )->
        groupBy( "machine_id" )->
        selectRaw( "max(machine_logs.id) as machine_log_id,machine_id, max(contour_sum_value) as contour_sum_value,max(created_at) as created_at" )->
        get()->toArray();


        $end_machine_logs = collect( $end_machine_logs )->keyBy( 'machine_id' );

        $station_ids       = [ 16 ]; // بافندگی
        $machine_type_list = MachineType::whereIn( "station_id", $station_ids )->pluck( "id" )->toArray();
        $machine_list      = Machine::whereIn( "machine_type_id", $machine_type_list )->get();
        $property          = MachinePropertyValue::
        whereIn( "machine_type_id", $machine_type_list )->
        where( "machine_property_id", 3 )->// تعداد قطب در ساعت
        pluck( "value", "machine_type_id" );

        $machine_efficiency = [];

        foreach ( $machine_list as $machine ) {


            $machine_start_date_time = isset( $start_machine_logs[ $machine->id ]["created_at"] ) ?
                $start_machine_logs[ $machine->id ]["created_at"] : null;
            $machine_start_date_time = Carbon::parse( $machine_start_date_time );

            $machine_end_date_time = isset( $end_machine_logs[ $machine->id ]["created_at"] ) ?
                $end_machine_logs[ $machine->id ]["created_at"] : null;
            $machine_end_date_time = Carbon::parse( $machine_end_date_time );

            $machine_diff_hours = $machine_end_date_time->diffInHours( $machine_start_date_time );
            if ( $machine_diff_hours == 0 ) {
                continue;
            }
            $start_value = isset( $start_machine_logs[ $machine->id ]["contour_sum_value"] ) ?
                $start_machine_logs[ $machine->id ]["contour_sum_value"] : null;

            $end_value = isset( $end_machine_logs[ $machine->id ]["contour_sum_value"] ) ?
                $end_machine_logs[ $machine->id ]["contour_sum_value"] : null;


            // تعداد کل قطب های زده شده در بازه زمانی
            $diff_value = 0;
            $t="";
            if ( isset( $start_value ) && isset( $end_value ) ) {
                $diff_value = $end_value - $start_value;
             //   $t=$diff_value."(start=*".$start_machine_logs[ $machine->id ]["contour_sum_value"]." - end=" .$end_machine_logs[ $machine->id ]["contour_sum_value"].")".$diff_value ;
;
            }




            // راندمان نوع 1
            $machine_efficiency[ $machine->id ]["efficiency_1"] =
            round( $diff_value /
                       ( $machine_diff_hours * $property[ $machine->machine_type_id ]) ,3)*100;


            $machine_efficiency[ $machine->id ]["machine_diff_hours"] = $machine_diff_hours;
            $machine_efficiency[ $machine->id ]["start_log_id"]       = isset( $start_machine_logs[ $machine->id ]["machine_log_id"] ) ? $start_machine_logs[ $machine->id ]["machine_log_id"] : "";
            $machine_efficiency[ $machine->id ]["end_log_id"]         = isset( $end_machine_logs[ $machine->id ]["machine_log_id"] ) ? $end_machine_logs[ $machine->id ]["machine_log_id"] : "";
            $machine_efficiency[ $machine->id ]["machine"]            = $machine;
            $machine_efficiency[ $machine->id ]["sum_contour"]        = $diff_value;
        }
        return $machine_efficiency;
    }
}
