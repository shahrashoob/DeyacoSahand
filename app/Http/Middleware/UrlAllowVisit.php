<?php

namespace App\Http\Middleware;

use App\Models\Contractor\Contractor;
use App\Models\Post\PostUser;
use App\Models\Utility\Menu\Menu;
use App\Models\Utility\Menu\MenuPost;
use App\Models\Worker;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class UrlAllowVisit {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure                 $next
     *
     * @return mixed
     */
    public function handle( Request $request, Closure $next ) {
        $route = Route::currentRouteName();

        if ( $user = \Auth::user() ) {
            $worker = Worker::find( $user->id );
            if ( ( $route != "login" && $route != "logout" && $route != "change_pass" && $route != "submit_change_pass" ) ) {
                if ( $worker->required_reset_password == 1 ) {
                    return redirect()->route( "change_pass" )->
                    withErrors( " با توجه به نکات امنیتی لازم است تا کلمه عبور خود را تغییر دهید." );
                }
                if ( $worker->entry_permit_status_id == 461000100  ) {
                    session( [
                        "error" => "شما مجوز ورود به سامانه را ندارید، لطفا با واحد منابع انسانی تماس بگیرید.",
                    ] );

                    return redirect()->route( "logout" );
                }

            }

        }

        // }
        //catch (\Exception $e) {

        //    return $next($request);
        // }
        if ( isset( Auth::user()->id ) ) {
            $post_users=Auth::user()->posts;
            $post_user = $post_users->first();

            view()->share( 'post_user', $post_user );
            //view()->share( 'post_users', $post_users );
        }

        return $next( $request );
    }
}
