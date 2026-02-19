<?php

namespace App\Http\Controllers\Utility\Notification;

use App\Http\Controllers\Controller;
use App\Models\Post\Post;
use App\Models\Utility\Notification\NotificationPost;
use App\Models\Utility\Notification\NotificationTemplate;
use App\Models\Utility\Notification\NotificationWorker;
use App\Models\Utility\Setting;
use App\Notifications\GroupUserNotification;
use App\Notifications\UserNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;

class DashboardController extends Controller {
    //
    public $view_path = "utility.notification.dashboard.";
    public $route_path = "utility.notification.dashboard.";

    public function index() {
        $post_user = Auth::user()->posts->first();
        $list      = NotificationTemplate::paginate(50);

        return view( $this->view_path . "index", compact( "list", "post_user" ) );
    }

    public function create() {
        $send_sms=Setting::find(8); // is_active_sms_module

        if(!isset($send_sms) ||( $send_sms && $send_sms->integer_value==0) ){
            return back()->withErrors("با توجه به تنظیمات سیستم امکان ارسال پیامک وجود ندارد.");
        }
        $post_list = Post::all();

        return view( $this->view_path . "create", compact( "post_list" ) );
    }

    public function store( Request $request ) {

        if ( $request->message == "" ) {
            return back()->withErrors( "لطفا متن پیامک را وارد نمایید." );
        }
        if ( ! isset( $request["data"] ) ) {
            return back()->withErrors( "لطفا حداقل یک پست جهت ارسال پیامک انتخاب کنید." );
        }
        $message   = $request->message;
        $post_list = [];
        foreach ( $request["data"]["post"] as $post_id => $value ) {
            $post_list[] = Post::find( $post_id );

        }

        return view( $this->view_path . "confirm", compact( "message", "post_list" ) );
    }

    public function confirm( Request $request ) {
        $mobile_list  = [];
        $message      = $request->message;
        $post_data    = [];
        $worker_data    = [];
        $notification = NotificationTemplate::create( [
            "caption" => "",
            "message" => $message
        ] );
        foreach ( $request["data"]["post"] as $post_id => $value ) {
            $post        = Post::find( $post_id );
            $post_data[] = [ "post_id" => $post_id, "notification_template_id" => $notification->id ];

            foreach ( $post->worker as $worker ) {
                if ( $worker->mobile != "" ) {
                    $mobile_list[] = $worker->mobile;
                    $worker_data[] = [
                        "user_id" => $post_id,
                        "notification_template_id" => $notification->id ,
                        "mobile"=>"00" . ( $worker->country->area_code ?? "98" ).$worker->mobile
                    ];

                }
            }
        }


        NotificationPost::insert( $post_data );
        NotificationWorker::insert( $worker_data );

        Notification::send( $notification, new GroupUserNotification( $worker_data, $message) );

       return redirect()->route($this->route_path."index")->with(["success"=>"پیامک با موفقیت به اپراتور ارسال شد."]);
    }
}
