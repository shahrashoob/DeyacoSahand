<?php

namespace App\Http\Controllers\HR\ShiftDelivery;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Models\LineProduct\Line;
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
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShiftDeliveryModule1Controller extends Controller {
    // تحویل شیفت بافندگی

    var $route_path = "hr.shift_delivery.module1.";
    var $view_path = "hr.shift_delivery.module1.";
    var $dashboard_path = "hr.personal.index";

    public function index( ShiftDeliveryModule $shift_delivery_module, Post $post ) {

        $worker = Worker::find( Auth::id() );

        $machine_list = ShiftDeliveryModule::getMachineListWhereWorkerIsOperator( $worker );;

        if ( count( $machine_list ) == 0 ) {
            return back()->withErrors( "با توجه به اینکه در حال حاضر شما اپراتور مسئول هیچ ماشینی نیستید، امکان تحویل شیفت برای شما وجود ندارد." );
        }
        $user_ids          = [];
        $option         [] = [ "value" => "", "caption" => "لطفا یک نفر را انتخاب کنید", "selected" => 1 ];
        $post_user_list    = $this->getPostList( $worker );
        foreach ( $post_user_list as $item ) {

            $user_ids_form_post_id_list=PostUser::where("post_id",$item->post_id)->
                get();
            foreach ($user_ids_form_post_id_list as $user_ids_form_post_id_item)
            // برای اینکه کاربر تکراری اضافه نشود.
            if ( ! in_array( $user_ids_form_post_id_item->user_id, $user_ids ) ) {

                if ( $shift_delivery_module->presence_in_the_organization_checked ) {
                    if ( $user_ids_form_post_id_item->worker->presentInOrganization() ) {
                        $option[]   = [ "value" => $user_ids_form_post_id_item->user_id, "caption" => $user_ids_form_post_id_item->worker->fullname() ];
                        $user_ids[] = $user_ids_form_post_id_item->user_id;
                    }
                } else {
                    $option[]   = [ "value" => $user_ids_form_post_id_item->user_id, "caption" => $user_ids_form_post_id_item->worker->fullname() ];
                    $user_ids[] = $user_ids_form_post_id_item->user_id;
                }
            }
        }

        $request = session( "request_shift_delivery_m1" );

        return view( $this->view_path . "index", compact( "request", "option", "machine_list", "worker", "post", "shift_delivery_module" ) );
    }

    public function submit( Request $request, ShiftDeliveryModule $shift_delivery_module, Post $post ) {


        $worker = Worker::find( Auth::id() );

        $machine_list = ShiftDeliveryModule::getMachineListWhereWorkerIsOperator( $worker );;

        if ( count( $machine_list ) == 0 ) {
            return back()->withErrors( "با توجه به اینکه در حال حاضر شما اپراتور مسئول هیچ ماشینی نیستید، امکان تحویل شیفت برای شما وجود ندارد." );
        }

        $message               = "";
        $machineLogList        = [];
        $LastLogMachineLogList = [];


        $worker_delivery_post = Worker::find( $request->user_id );
        if ( ! $worker_delivery_post ) {
            $message .= "لطفا یک همکار را انتخاب نمایید." . "<br/>";
        }
        if ( $shift_delivery_module->presence_in_the_organization_checked && ! $worker_delivery_post->presentInOrganization() ) {
            $message .= "با توجه به اینکه همکار مورد نظر در سازمان حضور ندارد، امکان تحویل شیف به ایشان امکان پذیر نیست." . "<br/>";
        }


        $post_user_list = $this->getPostList( $worker, "no_all" );

        $post_user_can_select = $this->getPostIdsForDelivery( $worker_delivery_post );

        $post_checked            = []; // آیا گروه های ماشین داخل این پست حداثل یک ماشین آن انتخاب شده است
        $post_checked2           = []; // به ازای هر پست ماشین های آن گروه را در آن ذخیره می کنیم.
        $post_machine_checked    = []; // لیست ماشین های هر پست
        $machine_permission_list = [];
        foreach ( $post_user_list as $post_user ) {

            if ( $post_user->post->shift_delivery_module_id == 1 ) {
                $list                                           = Line::getAllowedMachine( [ $post_user->post_id ] );
                $machine_permission_list[ $post_user->post_id ] = $list;
                foreach ( $list as $machine_id ) {
                    $post_machine_checked[ $machine_id ]                 = false;
                    $post_checked[ $machine_id ]                         = $post_user->post_id;
                    $post_checked2[ $post_user->post_id ][ $machine_id ] = $post_user->post_id;
                }
            }
        }

        $post_checked3 = [];
        foreach ( $post_user_can_select as $post_id => $item ) {
            if ( isset( $post_checked2[ $post_id ] ) ) {
                $post_checked3[ $post_id ] = $post_checked2[ $post_id ];
            }
        }

        $post_checked4 = [];
        foreach ( $post_checked3 as $post_id => $item ) {
            foreach ( $item as $machine_id => $p ) {
                $post_checked4[ $machine_id ] = $p;
            }
        }


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


        // چک کردن اینکه همه ماشین های یک پست یکجا تحویل شوند.
        $post_delivery_group = [];

        foreach ( $post_machine_checked as $machine_id => $value ) {
            if ( $value ) {
                if ( isset( $post_checked4[ $machine_id ] ) ) {
                    $post_id = $post_checked4[ $machine_id ];

                    // اگر پست جزء پست های مجاز فرد می باشد، لیست ماشین های آن چک می شود.
                    $post_delivery_group[ $post_id ] = true;

                } else {
                    $machine_ = Machine::find( $machine_id );
                    $message  .= " با توجه به پست های سازمانی " . $worker_delivery_post->fullname() . " امکان تحویل ماشین " . $machine_->caption . " به ایشان وجود ندارد." . "<br/>";
                }

            }
        }

        $message_machine_list = [];
        foreach ( $post_delivery_group as $post_id => $value ) {
            if ( $value ) {
                foreach ( $machine_permission_list[ $post_id ] as $machine_id ) {
                    if ( ! $post_machine_checked[ $machine_id ] ) {
                        $message_machine_list[ $post_id ][] = $machine_id;

                    }
                }
            }
        }

        if ( count( $message_machine_list ) > 0 ) {
            foreach ( $message_machine_list as $post_id => $machine_list_not_checked ) {
                $machine_list_not_checked = Machine::whereIn( "id", $machine_list_not_checked )->get();
                $post                     = Post::find( $post_id );
                $message                  .= "با توجه به اینکه ماشین های زیر در پست " . $post->caption . " می باشد، شما نمی توانید آنها را به صورت مجزا تحویل دهید، لطفا کنتور همه ماشین های زیر را وارد نمایید. ";

                foreach ( $machine_list_not_checked as $item ) {
                    $message .= "<br/>" . $item->caption;
                }
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

        if ( ! $shift_delivery_module->people_who_have_a_delivery_shift_have_permission_to_exit ) {

            $worker_delivery_post->exit_permit_status_id = 461000100;// مجوز خروج ندارد
            $worker_delivery_post->save();
        }
        $show_efficiency_to_operator = Setting::getIntegerValue( "show_efficiency_to_operator" );//

        if ( $show_efficiency_to_operator ) {

            return redirect()->route( $this->route_path . "show_efficiency", [ $shift_delivery_module, $post ] );
        } else {

            return redirect()->route( $this->dashboard_path, [
                $worker,
                $worker->random
            ] )->with( [ "success" => "تحویل شیفت با موفقیت انجام شد، لطفا جهت تایید تحویل شیفت به همکار (" . $worker_delivery_post->fullname() . ") اطلاع دهید." ] );
        }

    }

    public
    function getPostList(
        $worker, $type = "all"
    ) {
        $post_list_result = [];

        // به دست آوردن پست های حال حاضر فرد که در آنها سمت دارد و رنگ شیفت آن سبز است.
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
            $post_user_list = PostUser::join( "posts", "posts.id", "post_id" )->
            where( "shift_delivery_module_id", 1 )->// ماژول بافندگی
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
            where( "start_datetime", "<=", Carbon::now()->addMinute(  $post_user->post->allowed_earlier_time_for_entry ?? 0 ) )->
            where( "end_datetime", ">=", Carbon::now()->addMinute(- $post_user->post->allowed_delay_time_for_entry ?? 0 ) )->
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

          if(!in_array($worker->status_id,[4620001,4620011]) ){ // ���� �� ������
            return back()->withErrors( "با توجه به اینکه وضعیت حضور شما 'حاضر در محل کار' نمی باشد، امکان تایید تحویل شیفت وجود ندارد." );
        }
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
            $new_machine_log->operator_id           = $worker->id;
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
