<?php

namespace App\Http\Controllers\Utility\OfficeAutomation;

use App\Events\HR\OfficeAutomationLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Utility\OfficeAutomation\OfficeAutomationAction;
use App\Models\Utility\OfficeAutomation\OfficeAutomationToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WorkDoneController extends Controller {
    //
    public $route_path = "utility.office_automation.work_done.";
    public $view_path = "utility.office_automation.work_done.";

    public function submit( Request $request, OfficeAutomationToDoList $office_automation_to_do_list ) {

        $current_user_id          = Auth::id();
        $office_automation_action = OfficeAutomationAction::where( [
            "office_automation_to_do_list_id" => $office_automation_to_do_list->id,
            "user_id"                         => $current_user_id
        ] )->
        // where( "user_id", "!=", $office_automation_to_do_list->office_automation_work->user_id )->
        first();
        if ( ! $office_automation_action ) {
            return back()->withErrors( "شما به این کار دسترسی ندارید." );
        }

        if ( ! in_array( $office_automation_to_do_list->status_id, [
            5250002, // در جریان
        ] ) ) {
            return back()->withErrors( "وضعیت کار جهت ثبت معتبر نمی باشد." );
        }
        $result = DashboardController::checkFiles( $request );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        $office_automation_action->status_id = 5250005; // در انتظار تایید انجام کار
        $office_automation_action->save();

        event( new OfficeAutomationLogEvent( $office_automation_to_do_list->office_automation_work, $office_automation_to_do_list, $office_automation_action, 5250003, $request->description, $request ) );

        DashboardController::sendSms( $office_automation_action, "workdone" );

        return back()->with( [ "success" => " انجام کار با موفقیت ثبت شد." ] );

    }
}
