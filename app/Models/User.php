<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use App\Models\Post\PostUser;
use App\Models\HR\User\UserEntryImage;

class User extends Authenticatable {
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use Loggable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'firstname',
        'lastname',
        "required_reset_password",
        "national_code"
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function posts() {
        return $this->hasMany( PostUser::class );
    }

    public function user_entry_images(){
        return $this->hasMany(UserEntryImage::class);
    }
    public function getNavBars( $worker = null ,$menu_id=null) {
        if ( $worker == null ) {
            $worker = Worker::find( Auth::id() );
        }

        $navs = [];
        $priority_list=[];

        $post_users = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_user_object" );


        // بررسی پست های کاربر
        foreach ( $post_users as $post_user ) {
            $menuList = $post_user->getMenuList( $worker );

            foreach ( $menuList as $key => $item ) {
                if($menu_id){
                    // اگر کاربر به این منو دسترسی داشت، لازم نیست بقیه کدها اجرا شوند.
                    if( $menu_id == $item->menu_id){
                           $navs[ $item->menu->menu_type_id ][ $item->menu_id ] = $item;
                           return $navs;
                    }
                }
                else {

                    if ( $item->menu && $item->menu->show_in_navbar == 1 ) {

                        $item->menu->badge                                   = $item->menu->getBadge( $this->id );
                        $navs[ $item->menu->menu_type_id ][ $item->menu_id ] = $item;
                    }
                }
            }
        }


        return $navs;
    }
//    public function get_menu_list() {
//
//        $menu_list=[];
//        $worker         = Worker::find( Auth::id() );
//
//        if(count(\Auth::user()->current_shift)>0){
//            $post_user=\Auth::user()->current_shift;
//            $navs=[];
//            foreach ($post_user as $item){
//                foreach ($item->post->permission as $key => $item) {
//                    if($item->menu && $item->menu->show_in_navbar==1  ){
//                        $navs[$item->menu->menu_type_id][$item->menu_id]=$item;
//                    }
//                }
//            }
//        }
//
//
//        $post_users    = $this->posts;
//        $current_post = [];
//        foreach ( $post_users as $post_user ) {
//
//            $post_entry_row = $post_user->post->post_entry_status()->where( "status_id", $worker->status_id ?? 0 )->first();
//            if ($post_entry_row && $post_entry_row->allow_show_personal_menu && $post_entry_row->allow_show_all_menu ) {
//                return $this->hasMany( MenuPost::class );
//            } elseif ($post_entry_row && $post_entry_row->allow_show_personal_menu && ! $post_entry_row->allow_show_all_menu ) {
//
//                 $this->hasMany( MenuPost::class )->where( "menu_id", 515 );// پرسنلی
//            } else {
//
//                 $this->hasMany( MenuPost::class )->where( "menu_id", 0 );// منو ها خالی باشد
//            }
//
//
//
//            $check = ShiftWorkDay::where( [
//                "shift_id"      => $post_user->post->shift_id,
//                "shift_work_id" => $post_user->shift_work_id
//            ] )->
//            where( "start_datetime", "<=", now() )->
//            where( "end_datetime", ">", now() )->exists();
//            if ( $check ) {
//                $current_post[] = $post_user->post_id;
//            }
//        }
////        return $this->hasMany( PostUser::class );
//        return $this->posts()->whereIn("post_id",$current_post);
//    }

    public function fullname() {
        return $this->firstname . " " . $this->lastname;
    }

    public function checkButtonPermission( $button_id ) {

        return $this->posts->first()->checkButtonPermission( $button_id );
    }
}
