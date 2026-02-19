<?php

namespace App\Http\Middleware;

use App\Models\Post\PostUser;
use App\Models\Utility\Menu\Menu;
use App\Models\Utility\Menu\MenuPost;
use Closure;
use Illuminate\Http\Request;

class UrlCheckMiddleware {
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     *
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle( Request $request, Closure $next, $route_name = "",$route2_name="" ) {

        $menu = Menu::where( "route", $route_name )->
        when($route2_name!="", function ( $query ) use ( $route2_name ) {
            return $query->orWhere( "route", $route2_name );
        })->
        first();
        if ( ! $menu ) {
            return redirect( "dashboard" )->withErrors( "آدرس اعتبار سنجی صفحه نادرست است، لطفا با پشتیبانی تماس بگیرید." );
        }
        if ( \Auth::user() ) {

            $post_user=PostUser::where( "user_id", \Auth::user()->id )->first();
            if ( ! $post_user ) {
                return redirect( "dashboard" )->withErrors( "برای شما هیچ پستی در سیستم تعریف نشده است، لطفا با پشتیبانی تماس بگیرید" );
            }

            $navs=\Auth::user()->getNavBars($post_user->worker,$menu->id);

            if ( !isset($navs[$menu->menu_type_id][$menu->id]) ) {
                // بررسی شرط دوم اگر وجود داشت
                if($route2_name!=""){

                    $menu = Menu::where( "route", $route2_name )->first();
                    if ( ! $menu ) {
                        return redirect( "dashboard" )->withErrors( "آدرس اعتبار سنجی صفحه نادرست است، لطفا با پشتیبانی تماس بگیرید." );
                    }
                    $navs=\Auth::user()->getNavBars($post_user->worker,$menu->id);
                    if ( isset($navs[$menu->menu_type_id][$menu->id]) ){
                        return $next( $request );
                    }
                }

                return redirect( "dashboard" )->withErrors( "شما اجازه دسترسی به این صفحه را ندارید" );
            }

        }

        return $next( $request );
    }
}
