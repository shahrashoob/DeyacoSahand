<?php

namespace App\Http\Controllers\Utility\OfficeAutomation;

use App\Events\HR\OfficeAutomationLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Utility\OfficeAutomation\OfficeAutomationAction;
use App\Models\Utility\OfficeAutomation\OfficeAutomationToDoList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RejectController extends Controller{

    public $route_path = "utility.office_automation.reject.";
    public $view_path = "utility.office_automation.reject.";

    public function submit(Request $request ) {

        $action = OfficeAutomationAction::find( $request->action_reject ?? 0 );
        if ( !$action ) {
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

        $action->status_id = 5250002; //در جریان
        $action->save();

        event( new OfficeAutomationLogEvent( $action->office_automation_work, $action->office_automation_to_do_list, $action,5250005,$request->description,$request ) );

        DashboardController::sendSms($action,"reject");
        return back()->with( [ "success" => " ارجاع کار با موفقیت ثبت شد." ] );

    }
}
