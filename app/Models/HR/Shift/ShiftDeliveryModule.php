<?php

namespace App\Models\HR\Shift;

use App\Models\LineProduct\Line;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Post\PostUser;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftDeliveryModule extends Model {
    use HasFactory;

    protected $fillable = [
        "presence_in_the_organization_checked", // آیا حضور فرد در سازمان چک شود
        "people_who_have_a_delivery_shift_have_permission_to_exit", //فردی که تحویل شیفت دارد، مجوز خروج دارد؟
        "people_in_the_next_delivery_have_permission_to_exit"
    ];


    public static function getMachineListWhereWorkerIsOperator( Worker $worker, $type = "list", $type_post_ids = "post_ids", $type_user_ids = "" ) {

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( $type_post_ids, $worker );

        if ( $type_user_ids != "" ) {
            $user_ids[] = - 1;
            $user_ids   = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( $type_user_ids, $worker );
        } else {
            $user_ids = [ $worker->id ];
        }


        $allowed_machine_ids = Line::getAllowedMachine( $post_ids );
        $allowed_machine_ids[]=-1;
        $machine_log_ids     = MachineLog::
        whereIn( "machine_id", $allowed_machine_ids )->
        groupBy( "machine_id" )->
        selectRaw( "max(id) as id" )->
        pluck( "id" );
        $machine_log_ids[]=-1;


        $machine_logs = MachineLog::
        whereIn( "id", $machine_log_ids )->
        whereIn( "operator_id", $user_ids )->
        where( "machine_event_type_id", "!=", 650 )-> // تحویل شیفت های تایید نشده
        pluck( "machine_id" )->toArray();

        if ( $type == "count" ) {
            return Machine::whereIn( "id", $machine_logs )->count();
        }

        return Machine::whereIn( "id", $machine_logs )->get();
    }

    public static function getMachineLogWhereWorkerIsOperatorByEventType( Worker $worker, $machine_event_type_id, $type = "list" ) {

        $worker_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "leaves_user_ids", $worker );
        //اگر فردی جانشین فردی دیگری است، باید بتواند دکمه تایید تحویل شیفت را ببیند
        $worker_ids[]    = $worker->id;
        $machine_log_ids = MachineLog::
        groupBy( "machine_id" )->
        selectRaw( "max(id) as id" )->
        pluck( "id" )->
        toArray();

        $machine_logs = MachineLog::
        whereIn( "id", $machine_log_ids )->
        when( $machine_event_type_id, function ( $query ) use ( $machine_event_type_id ) {
            $query->where( "machine_event_type_id", $machine_event_type_id );
        } )->
        whereIn( "operator_id", $worker_ids )->
        get();


        if ( $type == "count" ) {
            return count( $machine_logs );
        }

        return $machine_logs;
    }
}
