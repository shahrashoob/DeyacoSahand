<?php

namespace App\Listeners\Product;

use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormLog;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Message;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Auth;

class ProductRequestFormLogListener
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
    public function handle(ProductRequestFormLogEvent $event)
    {
        $msg = null;
        if ( $event->text != "" ) {
            $msg = Message::create(
                [
                    "text"            => $event->text,
                    "other_id"        => $event->request_form->id,
                    "message_type_id" => 230
                ]
            );
        }
        $json_data=null;
        if ( $event->json_data != "" ) {
            $json_data = JsonDataList::create(
                [
                    "data"            => json_encode($event->json_data),
                    "other_id"        => $event->request_form->id,
                    "message_type_id" => 230
                ]
            );
        }
;
        ProductRequestFormLog::create( [
            "product_request_form_id"    => $event->request_form->id,

            "status_id" => $event->request_form->status_id,
            "user_id"   =>$event->user_id?? Auth::user()->id,
            "message_id"=>$msg->id??null,
            "json_data_list_id"=>$json_data->id??null,
            "form_id"=>$event->form_id??null,
            "event_id"=>$event->event_id??null
        ] );
    }
}
