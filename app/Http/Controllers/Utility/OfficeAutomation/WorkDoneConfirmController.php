<?php

namespace App\Http\Controllers\Utility\OfficeAutomation;

use App\Events\HR\OfficeAutomationLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Utility\OfficeAutomation\OfficeAutomationAction;
use App\Models\Utility\OfficeAutomation\OfficeAutomationToDoList;
use App\Models\Utility\OfficeAutomation\OfficeAutomationUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkDoneConfirmController extends Controller {
    public $route_path = "utility.office_automation.work_done_confirm.";
    public $view_path = "utility.office_automation.work_done_confirm.";

    public function submit( Request $request ) {

        $action = OfficeAutomationAction::find( $request->action_confirm ?? 0 );
        if ( ! $action ) {
            return back()->withErrors( "اطلاعات به درستی وارد نشده است، لطفا دوباره تلاش کنید." );
        }
        if ( $action->office_automation_to_do_list->user_id != Auth::id() ) {
            return back()->withErrors( "شما به این کار دسترسی ندارید." );
        }

        if ( ! in_array( $action->status_id, [
            5250005, // در انتظار تایید انجام کار
        ] ) ) {
            return back()->withErrors( "وضعیت کار جهت ثبت معتبر نمی باشد." );
        }
        $result = DashboardController::checkFiles( $request );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        $action->status_id = 5250004; //خاتمه یافته
        $action->save();

        // کسی که اکشن را انجام داده خاتمه یافته می شود.
        $office_automation_user=OfficeAutomationUser::where([
            "office_automation_work_id"=>$action->office_automation_work_id,
            "user_id"      => $action->user_id,
        ])->first();

        if($office_automation_user){
            $office_automation_user->status_id =  5250004; //خاتمه یافته
            $office_automation_user->save();
        }

        // خاتمه یافته کردن ارجاع ها
        $count = OfficeAutomationAction::
        where( "office_automation_to_do_list_id", $action->office_automation_to_do_list_id )->
        where( "status_id",  5250002 )-> // در جریان
        count();

        if ( $count == 0 ) {
            $action->office_automation_to_do_list->status_id = 5250004; //خاتمه یافته
            $action->office_automation_to_do_list->save();

        }

        // خاتمه یافته کردن کار
        $count=OfficeAutomationToDoList::
        where("office_automation_work_id",$action->office_automation_work_id)->
        where( "status_id",  5250002 )-> // در جریان
        count();
        if($count ==0){
            $action->office_automation_work->status_id = 5250004; //خاتمه یافته
            $action->office_automation_work->save();


            // کسی که  را انجام داده خاتمه یافته می شود.
            $office_automation_user=OfficeAutomationUser::where([
                "office_automation_work_id"=>$action->office_automation_work_id,
                "user_id"      => $action->office_automation_work->user_id,
            ])->first();

            if($office_automation_user){
                $office_automation_user->status_id =  5250004; //خاتمه یافته
                $office_automation_user->save();
            }
        }



        event( new OfficeAutomationLogEvent( $action->office_automation_work, $action->office_automation_to_do_list, $action, 5250004, $request->description, $request ) );

        DashboardController::sendSms($action,"confirm");
        return back()->with( [ "success" => " انجام کار با موفقیت ثبت شد." ] );

    }
}
