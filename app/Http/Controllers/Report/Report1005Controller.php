<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Station;
use App\Models\Report\R1005\Report1005;
use App\Models\Report\R1005\Report1005Line;
use App\Models\Utility\DateTime;
use App\Models\Utility\Option;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Report1005Controller extends Controller {
    //
    public function index() {
        $worker_option    = Option::get( "worker" );
        $report_1005_line = new Report1005Line();


        $machine_type_list = MachineType::where( "active_status_id", "1200" )->get();

        return view( "report/1005/index", compact( "worker_option", "machine_type_list", "report_1005_line" ) );
    }

    public function submit( Request $request ) {
//return $request->all();
        set_time_limit( 300 );// چون ممکن است زمان گزارش گیری خیلی زیاد شود.
        $start_date_time = DateTime::getDateTimeFromRequest( $request, "start_date" );
        $end_date_time   = DateTime::getDateTimeFromRequest( $request, "end_date" );

        $machine_ids = Report1005Line::getAllowedMachine();

        $user_id = $request->user_id;
        $list    = MachineLog::
        whereIn( "machine_id", $machine_ids )->
        where( "created_at", ">=", $start_date_time )->
        where( "created_at", "<=", $end_date_time )->
        select( 'machine_id', "user_id"
            , "production_status_id", "on_status_id", "active_status_id", "maintenance_status_id",
            "machine_event_type_id", "machine_off_reason_id", "contour_sum_value" )
                             ->
                             selectRaw( 'LEAD(created_at,1) OVER (
                                        PARTITION BY machine_id
                                        ORDER BY id ) end_date' )->
            selectRaw( Auth::id() . ' as  owner_user_id, created_at as start_date' )->
            get()->
            toArray();

        Report1005::where( "owner_user_id", Auth::id() )->delete();

        $limit = 300;
        if ( count( $list ) == 0 ) {
            return back()->withErrors( "هیچ رکوردی جهت نمایش گزارش یافت نشد." );
        }
        if ( count( $list ) > $limit ) {
            for ( $k = 0; $k < count( $list ); $k = $k + $limit ) {
                $new_list = array_slice( $list, $k, $limit );
                Report1005::insert( $new_list );
            }
        } else {
            Report1005::insert( $list );
        }
        $end_date = Carbon::now();
        $end_date = $end_date->greaterThan( $end_date_time ) ? $end_date_time : $end_date;
        Report1005::whereNull( "end_date" )->update( [ "end_date" => $end_date ] );

        DB::statement( "update report_1005 set time_in_second= TIME_TO_SEC(TIMEDIFF(end_date, start_date))" );


        $time_list_production_status = Report1005::join( "status", "status.id", "production_status_id" )->
        where( "owner_user_id", Auth::id() )->
        when( $user_id, function ( $query ) use ( $user_id ) {
            return $query->where( "user_id", $user_id );
        } )->
        groupBy( "production_status_id" )->
        selectRaw( "status.caption,round(sum(time_in_second)/3600) as value " )->get();

        $sum_all = Report1005::join( "status", "status.id", "production_status_id" )->
            where( "owner_user_id", Auth::id() )->
            when( $user_id, function ( $query ) use ( $user_id ) {
                return $query->where( "user_id", $user_id );
            } )->
            sum( "time_in_second" ) / 3600;

        $time_list_production_status_radar = Report1005::join( "status", "status.id", "production_status_id" )->
        where( "owner_user_id", Auth::id() )->
        when( $user_id, function ( $query ) use ( $user_id ) {
            return $query->where( "user_id", $user_id );
        } )->
        groupBy( "production_status_id" )->
        selectRaw( "status.caption,sum(time_in_second)/(3600*" . $sum_all . ")*100 as value " )->get();

        $time_list_on_status = Report1005::join( "status", "status.id", "on_status_id" )->
        where( "owner_user_id", Auth::id() )->
        when( $user_id, function ( $query ) use ( $user_id ) {
            return $query->where( "user_id", $user_id );
        } )->
        groupBy( "on_status_id" )->
        selectRaw( "status.caption,round(sum(time_in_second)/3600)  as value" )->get();

        $time_list_off_reason = Report1005::join( "machine_off_reasons", "machine_off_reasons.id", "machine_off_reason_id" )->
        where( "owner_user_id", Auth::id() )->
        when( $user_id, function ( $query ) use ( $user_id ) {
            return $query->where( "user_id", $user_id );
        } )->
        groupBy( "machine_off_reason_id" )->
        selectRaw( "caption,round(sum(time_in_second)/3600)  as value" )->get();

        $time_list_machine_type = Report1005::

        join( "machines", "machines.id", "machine_id" )->
        join( "machine_types", "machine_types.id", "machine_type_id" )->
        where( "owner_user_id", Auth::id() )->
        when( $user_id, function ( $query ) use ( $user_id ) {
            return $query->where( "user_id", $user_id );
        } )->
        groupBy( "machine_type_id" )->
        selectRaw( "machine_types.caption,round(sum(time_in_second)/3600)  as value" )->get();

        return view( "report.1005.show_report", compact(
            "time_list_production_status",
            "time_list_on_status", "time_list_off_reason",
            "time_list_production_status_radar",
            "time_list_machine_type" ) );
    }

    public function add_access( $line_id, $station_id, $machine_type_id, $machine_id ) {
        $user_id = Auth::id();
        if ( $line_id != 0 ) {
            Report1005Line::where( [ "user_id" => $user_id, "line_id" => $line_id ] )->delete();
            Report1005Line::create( [ "user_id" => $user_id, "line_id" => $line_id ] );
        }

        if ( $station_id != 0 ) {
            $station = Station::find( $station_id );
            Report1005Line::where( [ "user_id" => $user_id, "station_id" => $station_id ] )->delete();
            Report1005Line::create( [
                "user_id"    => $user_id,
                "line_id"    => $station->line_id,
                "station_id" => $station_id
            ] );
        }

        if ( $machine_type_id != 0 ) {
            $machine_type = MachineType::find( $machine_type_id );
            Report1005Line::where( [ "user_id" => $user_id, "machine_type_id" => $machine_type_id ] )->delete();
            Report1005Line::create( [
                "user_id"         => $user_id,
                "line_id"         => $machine_type->station->line_id,
                "station_id"      => $machine_type->station_id,
                "machine_type_id" => $machine_type->id
            ] );
        }


        return redirect()->back()->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function remove_access( $line_id, $station_id, $machine_type_id, $machine_id ) {
        $user_id = Auth::id();
        if ( $line_id != 0 ) {
            Report1005Line::where( [ "user_id" => $user_id, "line_id" => $line_id ] )->delete();
        }
        if ( $station_id != 0 ) {
            Report1005Line::where( [ "user_id" => $user_id, "station_id" => $station_id ] )->delete();
        }
        if ( $machine_type_id != 0 ) {
            Report1005Line::where( [ "user_id" => $user_id, "machine_type_id" => $machine_type_id ] )->delete();
        }


        return redirect()->back()->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function manage_access( $line_id, $station_id, $machine_type_id, $machine_id ) {


        $report_1005_line = new Report1005Line();
        if ( $line_id != 0 ) {
            $line         = Line::find( $line_id );
            $station_list = Station::where( "line_id", $line_id )->get();

            return view( "report.1005.station_permission", compact( "line", "station_list", "report_1005_line" ) );

        } elseif ( $station_id != 0 ) {

            $station           = Station::find( $station_id );
            $machine_type_list = MachineType::where( "station_id", $station_id )->get();

            return view( "report.1005.machine_type_permission", compact( "station", "machine_type_list", "report_1005_line" ) );

        } else if ( $machine_type_id != 0 ) {
            $machine_type = MachineType::find( $machine_type_id );
            $machine_list = Machine::where( "machine_type_id", $machine_type_id )->get();

            return view( "report.1005.machine_permission", compact( "machine_type", "machine_list", "report_1005_line" ) );

        }


    }

    public function machine_permissionRequest( $request, Post $post, MachineType $machine_type ) {

        LinePost::where( [ "post_id" => $post->id, "machine_type_id" => $machine_type->id ] )->delete();

        $data = $request->data;
        if ( isset( $data["machine"] ) ) {
            foreach ( $data["machine"] as $key => $item ) {
                LinePost::create( [
                    "post_id"         => $post->id,
                    "line_id"         => $machine_type->station->line_id,
                    "station_id"      => $machine_type->station_id,
                    "machine_type_id" => $machine_type->id,
                    "machine_id"      => $key
                ] );
            }

            return redirect()->back();
        }
    }

    public function machine_permission( Request $request, MachineType $machine_type ) {

        $user_id = Auth::id();

        Report1005Line::where( [ "user_id" => $user_id, "machine_type_id" => $machine_type->id ] )->delete();

        $data = $request->data;
        if ( isset( $data["machine"] ) ) {
            foreach ( $data["machine"] as $key => $item ) {
                Report1005Line::create( [
                    "user_id"         => $user_id,
                    "line_id"         => $machine_type->station->line_id,
                    "station_id"      => $machine_type->station_id,
                    "machine_type_id" => $machine_type->id,
                    "machine_id"      => $key
                ] );
            }
        }

        return redirect()->route( "report.1005.index" )->with( [ "success" => count( $data["machine"] ) . " ماشین به لیست انتخاب ها اضافه شدند. " ] );


    }


}
