<?php

namespace App\Listeners\HR;

use App\Events\HR\OfficeAutomationLogEvent;
use App\Models\File\File;
use App\Models\Utility\Message;
use App\Models\Utility\OfficeAutomation\OfficeAutomationFile;
use App\Models\Utility\OfficeAutomation\OfficeAutomationLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class OfficeAutomationLogListener {
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {
        //
    }

    /**
     * Handle the event.
     *
     * @param object $event
     *
     * @return void
     */
    public function handle( OfficeAutomationLogEvent $event ) {

        $user_id=Auth::id();
        $log                                         = new OfficeAutomationLog();

        $log->office_automation_work_id              = $event->office_automation_work->id;
        $log->office_automation_to_do_list_id        = $event->office_automation_to_do_list->id ?? null;
        $log->office_automation_action_id       = $event->office_automation_action->id ?? null;

        $log->office_automation_work_status_id       = $event->office_automation_work->status_id;
        $log->office_automation_to_do_list_status_id = $event->office_automation_to_do_list->status_id ?? null;
        $log->office_automation_action_status_id = $event->office_automation_action->status_id ?? null;

        $log->event_id                               = $event->event_id;

        $log->user_id                                = $user_id;

        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->office_automation_work->id,
                    "message_type_id" => 200
                ]
            );
        }

        $log->message_id = $msg->id ?? null;
        $log->save();

        // بارگذاری فایل ها
        if ( $event->request && isset( $event->request->work_file ) ) {
                foreach($event->request->file( 'work_file' ) as $work_file_item ) {
                    $file = File::uploadFile(
                        $work_file_item,
                        $event->office_automation_work->id . "_" . rand( 1000, 10000 ) . "." . File::get_file_extension($work_file_item->getClientOriginalName()),
                        50,
                        "upload/office_automation_work/",
                        true );

                    OfficeAutomationFile::create( [
                        "office_automation_work_id"       => $event->office_automation_work->id,
                        "office_automation_to_do_list_id" => $event->office_automation_to_do_list->id ?? null,
                        "office_automation_log_id"        => $log->id,
                        "user_id"                         => $user_id,
                        "file_id"                         => $file->id
                    ] );
                }

        }


    }
}
