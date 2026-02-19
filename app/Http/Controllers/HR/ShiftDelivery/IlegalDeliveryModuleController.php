<?php

namespace App\Http\Controllers\HR\ShiftDelivery;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LinePost;
use App\Models\LineProduct\Machine\Allocation\MachineAllocationMaterialConsumed;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Post\Post;
use App\Models\Post\PostUser;
use App\Models\HR\LeaveOvertime\LeaveOvertime;
use App\Models\HR\Shift\ShiftDeliveryModule;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Carbon\Carbon;
use http\Exception\BadConversionException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IlegalDeliveryModuleController extends Controller {
    // تحویل شیفت بافندگی

    var $route_path = "hr.shift_delivery.illegal_delivery.";
    var $view_path = "hr.shift_delivery.illegal_delivery.";
    var $dashboard_path = "hr.personal.index";

    public function index( ShiftDeliveryModule $shift_delivery_module, Post $post ) {

        $worker = Worker::find( Auth::id() );


        $user_ids          = [];
        $option         [] = [ "value" => "", "caption" => "لطفا یک نفر را انتخاب کنید", "selected" => 1 ];

        // لیست ماشین هایی که تحویل یک فرد است، ولی نباید تحویل او باشد.
        $illegal_delivery_machine_ids = IlegalDeliveryModuleController::illegal_machine_ids( $worker );
        if ( count( $illegal_delivery_machine_ids ) == 0 ) {
            return back()->withErrors( "هیچ ماشین وجود ندارد که  به صورت غیر مجاز تحویل شما شده باشد" );
        }

        // به دست آوردن لیست پست هایی که ماشین های غیر مجاز در یکی از آنها قرار دارد.
        $machine_list     = Machine::whereIn( "id", $illegal_delivery_machine_ids )->get();
        $machine_ids    = Machine::whereIn( "id", $illegal_delivery_machine_ids )->pluck("id","id")->toArray();
        $machine_type_ids = Machine::whereIn( "id", $illegal_delivery_machine_ids )->pluck( "machine_type_id" )->toArray();
        $station_ids      = Machine::whereIn( "id", $illegal_delivery_machine_ids )->pluck( "station_id" )->toArray();

        if(count($station_ids) ==0){
            return back()->withErrors("ماشین های غیر مجاز تحویل شده به شما، در هیچ یک از ایستگاه های کاری وجود ندارد، لطفا با پشتیبانی تماس بگیرید.");
        }
        if(count($machine_list) ==0){
            return back()->withErrors("هیچ ماشینی تحویل شما نمی باشد.");
        }

        $post_list   = LinePost::
        orWhereIn( "machine_type_id", $machine_type_ids )->
        whereIn( "machine_id", $machine_ids )->
        orWhereIn( "station_id", $station_ids )->
        distinct( "post_id" )->
        pluck( "post_id" )->toArray();
        $post_list[] = - 1;

        $post_user_list = PostUser::
        join( "posts", "post_id", "posts.id" )->
        whereIn( "post_id", $post_list )->
        where( "shift_delivery_module_id", ">", 0 )->get();
        foreach ( $post_user_list as $item ) {

            // برای اینکه کاربر تکراری اضافه نشود.
            if ( ! in_array( $item->user_id, $user_ids ) ) {

                if ( $shift_delivery_module->presence_in_the_organization_checked ) {
                    if ( $item->worker->presentInOrganization() ) {
                        $option[]   = [ "value" => $item->user_id, "caption" => $item->worker->fullname() ];
                        $user_ids[] = $item->user_id;
                    }
                } else {
                    $option[]   = [ "value" => $item->user_id, "caption" => $item->worker->fullname() ];
                    $user_ids[] = $item->user_id;
                }
            }
        }
        $request = session( "request_shift_illegal_delivery" );

        return view( $this->view_path . "index", compact( "request", "option", "machine_list", "worker", "post", "shift_delivery_module" ) );
    }

    public function submit( Request $request, ShiftDeliveryModule $shift_delivery_module, Post $post ) {


        $worker = Worker::find( Auth::id() );


        $message               = "";
        $machineLogList        = [];
        $LastLogMachineLogList = [];

        $illegal_delivery_machine_ids = IlegalDeliveryModuleController::illegal_machine_ids( $worker );
        if ( count( $illegal_delivery_machine_ids ) == 0 ) {
            return back()->withErrors( "هیچ ماشین وجود ندارد که  به صورت غیر مجاز تحویل شما شده باشد" );
        }

        $worker_delivery_post = Worker::find( $request->user_id );
        if ( ! $worker_delivery_post ) {
            $message .= "لطفا یک همکار را انتخاب نمایید." . "<br/>";
        }
        if ( $shift_delivery_module->presence_in_the_organization_checked && ! $worker_delivery_post->presentInOrganization() ) {
            $message .= "با توجه به اینکه همکار مورد نظر در سازمان حضور ندارد، امکان تحویل شیف به ایشان امکان پذیر نیست." . "<br/>";
        }

        $machine_list = Machine::whereIn( "id", $illegal_delivery_machine_ids )->get();


        foreach ( $machine_list as $machine ) {

            $contour_1_id = "machine_" . $machine->id . "contour_" . 1 . "_value";
            $contour_2_id = "machine_" . $machine->id . "contour_" . 2 . "_value";
            $contour_3_id = "machine_" . $machine->id . "contour_" . 3 . "_value";
            $contour_4_id = "machine_" . $machine->id . "contour_" . 4 . "_value";
            $contour_5_id = "machine_" . $machine->id . "contour_" . 5 . "_value";

            // آیا ماشین تحویل شده است؟
            if ( $request->$contour_1_id ) {
                $last_row_log = MachineLog::getLastLogWithContour( $machine );

                $LastLogMachineLogList[ $machine->id ] = $last_row_log;

                $contour_result = $last_row_log->checkMinContour(
                    $request->$contour_1_id,
                    $request->$contour_2_id,
                    $request->$contour_3_id,
                    $request->$contour_4_id,
                    $request->$contour_5_id
                );


                if (
                    isset( $last_row_log ) && ! $contour_result["result"]
                ) {
                    $message .= $machine->caption . ":" . $contour_result["error"] . "<br/>";
                } else {
                    // ثبت اولیه لاگ ماشین
                    $machineLog                        = new MachineLog();
                    $machineLog->machine_event_type_id = 650; // ثبت تحویل شیفت
                    $machineLog->contour_1_value       = $request->$contour_1_id * $contour_result["ratio"];
                    $machineLog->contour_2_value       = $request->$contour_2_id * $contour_result["ratio"];
                    $machineLog->contour_3_value       = $request->$contour_3_id * $contour_result["ratio"];
                    $machineLog->contour_4_value       = $request->$contour_4_id * $contour_result["ratio"];
                    $machineLog->contour_5_value       = $request->$contour_5_id * $contour_result["ratio"];
                    $machineLog->operator_id           = $worker_delivery_post->id;

                    $machineLogList[ $machine->id ] = $machineLog;
                }


                $post_machine_checked[ $machine->id ] = true;

            }
        }


        session( [
            "request_shift_delivery_m1" => null,
        ] );

        if ( $message != "" ) {
            session( [
                "request_shift_delivery_m1" => $request->all(),
            ] );

            return back()->withErrors( $message );
        }

        foreach ( $machine_list as $machine ) {

            if ( isset( $post_machine_checked[ $machine->id ] ) && $post_machine_checked[ $machine->id ] ) {

                event( new MachineLogEvent( $machine, $machineLogList[ $machine->id ] ) );

                // ثبت مقدار مصرف
                MachineAllocationMaterialConsumed::registerNewConsumed( null, $machine, null, null );
            }

        }

        return redirect()->route( $this->dashboard_path, [
            $worker,
            $worker->random
        ] )->with( [ "success" => "تحویل شیفت با موفقیت انجام شد، لطفا جهت تایید تحویل شیفت به همکار (" . $worker_delivery_post->fullname() . ") اطلاع دهید." ] );


    }

    public static function illegal_machine_ids( $worker ) {
        $machine_log_ids   = MachineLog::
        groupBy( "machine_id" )->
        where( "operator_id", $worker->id )->
        pluck( "machine_id" )->
        toArray();
        $machine_log_ids[] = - 1;


        $allowed_machine_ids  = Line::getAllowedMachine();
        $illegal_delivery_ids = [];
        foreach ( $machine_log_ids as $machine_id ) {

            if ( ! in_array( $machine_id, $allowed_machine_ids ) and $machine_id != - 1 ) {
                $last_log = MachineLog::where( "machine_id", $machine_id )->orderByDesc( "id" )->first();
                if ( $last_log && $last_log->operator_id==$worker->id  ) {
                    $illegal_delivery_ids[] = $machine_id;
                }
            }
        }

        return $illegal_delivery_ids;
    }

    public
    function getPostList(
        $worker, $type = "all"
    ) {
        $post_list_result = [];

        $postList = PostUser::join( "posts", "posts.id", "post_id" )->
        where( "shift_delivery_module_id", 1 )->// ماژول بافندگی
        where( "user_id", $worker->id )->
        pluck( "post_id" )->toArray();

        $option            = [];
        $user_ids          = [];
        $option         [] = [ "value" => "", "caption" => "لطفا یک نفر را انتخاب کنید", "selected" => 1 ];

        //   بررسی اینکه آیا فرد جانشین برای پست است یا خیر
        $leave_list = LeaveOvertime::
        join( "leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id" )->
        where( "replace_user_id", $worker->id )->
        whereIn( "leave_overtimes.status_id", [ 4630003, 4630006, 4630007 ] )->
        where( "start_datetime", "<=", Carbon::now() )->
        where( "end_datetime", ">=", Carbon::now() )->
        get();

        // در صورتی که فرد جانشین فرد دیگر باشد، باید بتواند به همه افرادی که در پست جانشین قرار دارند، تحویل دهد.
        foreach ( $leave_list as $leave_item ) {
            $post_user      = PostUser::find( $leave_item->post_user_id );
            $post_user_list = PostUser::
            where( "post_id", $post_user->post_id ?? 0 )->
            where( "user_id", "!=", $worker->id )->
            groupBy( "user_id" )->
            get();
            foreach ( $post_user_list as $item ) {
                $post_list_result[ $item->id ] = $item;
            }
//            $result    = $this->add_post_user_to_list( $shift_delivery_module, $post_user_list, $user_ids, $option, 1 );
//            $option    = $result["option"];
//            $user_ids  = $result["user_ids"];
        }

        $post_user_list = PostUser::
        whereIn( "post_id", $postList )->
        where( "user_id", "!=", $worker->id )->
        get();

// اضافه کردن جانشین هایی که با خود فرد هم گروه هستند.
        foreach ( $post_user_list as $item ) {
            $item->worker_delivery_user_id = $item->user_id;
            $post_list_result[ $item->id ] = $item;
        }
//        $result   = $this->add_post_user_to_list( $shift_delivery_module, $post_user_list, $user_ids, $option, 1 );
//        $option   = $result["option"];
//        $user_ids = $result["user_ids"];

        $post_list_result_end = $post_list_result;
        foreach ( $post_list_result as $post_user ) {

            //   بررسی اینکه آیا برای این post_user مرخصی ثبت شده است یا خیر
            $leave_list = LeaveOvertime::
            join( "leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id" )->
            where( "user_id", $post_user->user_id )->
            whereIn( "leave_overtimes.status_id", [ 4630003, 4630006, 4630007 ] )->
            where( "start_datetime", "<=", Carbon::now()->addMinute( - $post_user->post->allowed_earlier_time_for_entry ?? 0 ) )->
            where( "end_datetime", ">=", Carbon::now()->addMinute( $post_user->post->allowed_delay_time_for_entry ?? 0 ) )->
            get();
//echo $post_user->user_id."<br/>";
            // در صورتی که فردی جانشین post_user باشد، باید بتواند به همه افرادی که در پست جانشین قرار دارند، تحویل دهد.
            foreach ( $leave_list as $leave_item ) {

                $post_user_list_leave = PostUser::where( "user_id", $leave_item->replace_user_id )->get();
                foreach ( $post_user_list_leave as $item ) {
                    $item->worker_delivery_user_id     = $leave_item->replace_user_id;
                    $post_list_result_end[ $item->id ] = $item;
                }

            }
        }

//echo "post_user".$post_user->post->allowed_earlier_time_for_entry;

        if ( $type == "all" ) {
            //   بررسی اینکه آیا برای خود فرد مرخصی ثبت شده است که تاریخ شروع آن بعد از تاریخ کنونی باشد.
            $leave_list = LeaveOvertime::
            join( "leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id" )->
            where( "user_id", $worker->id )->
            whereIn( "leave_overtimes.status_id", [ 4630003, 4630006, 4630007 ] )->
            where( "start_datetime", ">=", Carbon::now() )->
            get();

// در صورتی که فردی جانشین post_user باشد، باید بتواند به همه افرادی که در پست جانشین قرار دارند، تحویل دهد.
            foreach ( $leave_list as $leave_item ) {
                //  echo $leave_item->id."<br/>";
                $post_user_list_leave = PostUser::where( "user_id", $leave_item->replace_user_id )->get();
                foreach ( $post_user_list_leave as $item ) {
                    $item->worker_delivery_user_id     = $leave_item->replace_user_id;
                    $post_list_result_end[ $item->id ] = $item;

                }

            }
        }

        return $post_list_result_end;
    }

    public
    function getPostIdsForDelivery(
        $worker
    ) {
        $post_list_result = [];

        $postList = PostUser::join( "posts", "posts.id", "post_id" )->
        where( "user_id", $worker->id )->
        get();

        foreach ( $postList as $item ) {
            $post_list_result[ $item->post_id ] = $item;
        }
        //   بررسی اینکه آیا فرد در آینده جانشین برای پست است یا خیر
        $leave_list = LeaveOvertime::
        join( "leave_overtime_confirmation", "leave_overtimes.id", "leave_overtime_id" )->
        where( "replace_user_id", $worker->id )->
        whereIn( "leave_overtimes.status_id", [ 4630003, 4630006, 4630007 ] )->
        where( "start_datetime", ">=", Carbon::now() )->
        get();

        // در صورتی که فرد جانشین فرد دیگر باشد، باید بتواند به همه افرادی که در پست جانشین قرار دارند، تحویل دهد.
        foreach ( $leave_list as $leave_item ) {
            $post_user                               = PostUser::find( $leave_item->post_user_id );
            $post_list_result[ $post_user->post_id ] = $post_user;

        }


        return $post_list_result;
    }

    public
    function waiting_delivery(
        ShiftDeliveryModule $shift_delivery_module, Post $post
    ) {
        $worker       = Worker::find( Auth::id() );
        $machine_logs = ShiftDeliveryModule::getMachineLogWhereWorkerIsOperatorByEventType( $worker, 650 );

        if ( count( $machine_logs ) == 0 ) {
            return back()->withErrors( "شما اپراتور مسئول هیچ ماشینی نمی باشید." );
        }

        return view( $this->view_path . "waiting_delivery", compact( "machine_logs", "worker", "post", "shift_delivery_module" ) );


    }

    public
    function submit_waiting_delivery(
        ShiftDeliveryModule $shift_delivery_module, Post $post
    ) {

        $worker = Worker::find( Auth::id() );

        $machine_logs = ShiftDeliveryModule::getMachineLogWhereWorkerIsOperatorByEventType( $worker, 650 );

        if ( count( $machine_logs ) == 0 ) {
            return back()->withErrors( "شما اپراتور مسئول هیچ ماشینی نمی باشید." );
        }
        foreach ( $machine_logs as $item ) {
            $new_machine_log                        = $item->replicate();
            $new_machine_log->contour_1_value       = null;
            $new_machine_log->contour_2_value       = null;
            $new_machine_log->contour_3_value       = null;
            $new_machine_log->contour_4_value       = null;
            $new_machine_log->contour_5_value       = null;
            $new_machine_log->contour_sum_value     = 0;
            $new_machine_log->machine_event_type_id = 660;// تایید تحویل شیفت
            event( new MachineLogEvent( $item->machine, $new_machine_log ) );

        }


        $worker->exit_permit_status_id = 461000200;// مجوز خروج دارد
        $worker->save();

        return redirect()->route( $this->dashboard_path, [
            $worker,
            $worker->random
        ] )->with( [ "success" => "تایید تحویل شیفت با موفقیت انجام شد." ] );
    }

    public
    function show_efficiency(
        ShiftDeliveryModule $shift_delivery_module, Post $post
    ) {

        $worker = Worker::find( Auth::id() );

        $machine_log_ids = MachineLog::
        groupBy( "machine_id" )->
        selectRaw( "max(id) as id" )->
        pluck( "id" )->
        toArray();

        $machine_logs = MachineLog::
        whereIn( "id", $machine_log_ids )->
        where( "machine_event_type_id", 650 )->
        where( "user_id", $worker->id )->
        get();

        if ( count( $machine_logs ) == 0 ) {
            // اگر برای اولین بار است که شیفت را تحویل می گیرد.
            return redirect()->route( $this->dashboard_path, [
                $worker,
                $worker->random
            ] )->with( [ "success" => "تحویل شیفت با موفقیت انجام شد، لطفا جهت تایید تحویل شیفت به همکار خود اطلاع دهید." ] );
        }


        $efficiency_sum   = 0;
        $efficiency_count = 0;
        foreach ( $machine_logs as $item ) {

            $last_machine_log = MachineLog::
            where( "machine_id", $item->machine_id )->
            where( "id", "<", $item->id )->
            where( "machine_event_type_id", 650 )-> // تحویل شیفت قبلی
            where( "user_id", $worker->id )->first();


            if ( $last_machine_log ) {

                $contour_in_minute = $item->machine->machine_type->get_property_value( 3, 1 ); //  کارکرد کنتور اصلی در ساعت

                $start  = Carbon::parse( $last_machine_log->created_at );
                $end    = Carbon::parse( $item->created_at );
                $minute = $start->diffInMinutes( $end );

                $operator_counter_count = $item->sumCounter() - $last_machine_log->sumCounter();

                $efficiency_sum += $operator_counter_count / ( $contour_in_minute * $minute );
                $efficiency_count ++;

            }
        }


        if ( $efficiency_count == 0 ) {
            return redirect()->route( $this->dashboard_path, [
                $worker,
                $worker->random
            ] )->with( [ "success" => "تحویل شیفت با موفقیت انجام شد، لطفا جهت تایید تحویل شیفت به همکار خود اطلاع دهید." ] );

        } else {

            $efficiency = $efficiency_sum / $efficiency_count;

            return view( $this->view_path . "show_efficiency", compact( "efficiency", "worker", "post", "shift_delivery_module" ) );

        }


    }

}
