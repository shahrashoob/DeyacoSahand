<?php

namespace App\Listeners\Utility;

use App\Events\Utility\SpecialLicenseEvent;
use App\Models\Utility\Message;
use App\Models\Utility\SpecialLicense\SpecialLicenseLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class SpecialLicenseListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(SpecialLicenseEvent $event)
    {
        //
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->special_license->id,
                    "message_type_id" => 260
                ]
            );
        }

        SpecialLicenseLog::create( [
            "special_license_id"            => $event->special_license->id,
            "status_id"           =>  $event->special_license->status_id,
            "event_id"           => $event->event_id,
            "message_id"          => $msg->id ?? null,
            "user_id"             =>$event->user_id?? Auth::user()->id,
            "post_id"=>$event->post_id,
            "committee_id"=>$event->committee_id
        ] );
    }
}
