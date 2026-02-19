<?php

namespace App\Models\Utility\PupUp;

use App\Models\Customer\Customer;
use App\Models\Post\PostUser;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class PupUp extends Model {
    use HasFactory;

    protected $fillable = [
        "caption",
        "message",
        "version",
        "number_of_show",
        "start_date",
        "end_of_date",
        "active_status_id"
    ];

    public function active_status() {
        return $this->belongsTo( Status::class, "active_status_id" );
    }

    public function start_datetime() {
        return jdate( Carbon::parse( $this->start_date )->timestamp )->format( 'Y/m/d ' );

    }

    public function end_datetime() {
        return jdate( Carbon::parse( $this->end_of_date )->timestamp )->format( 'Y/m/d ' );

    }

    public static function get_current_pup_up( $remove_id = null ) {

        $user_id  = Auth::user()->id;
        $customer = Customer::where( "user_id", $user_id )->first();

        $post_user = PostUser::where( "user_id", $user_id )->first();
        $post_id   = 0;
        if ( $post_user ) {
            $post_id = $post_user->post_id;
        }

        if ( ! isset( $remove_id ) ) {
            $remove_id = [];
        }

        $list = PupUp::
        where( "start_date", "<=", Carbon::now() )->
        where( "end_of_date", ">=", Carbon::now()->addDay( - 1 ) )->
        get();

        foreach ( $list as $item ) {
            $pup_up_post = PupUpPost::firstOrCreate( [
                "pup_up_id" => $item->id,
                "user_id"   => $user_id
            ] );

            $post_show_ids = explode( ",", $item->post_show_ids );

            // بررسی پست  و حذف پاپاپ هایی که قبلا نمایش داده شده
            if (
                in_array( $post_id, $post_show_ids ) &&
                $pup_up_post->number < $item->number_of_show &&
                ! in_array( $item->id, $remove_id )
            ) {
                return $item;

            }
        }

        return null;
    }
}
