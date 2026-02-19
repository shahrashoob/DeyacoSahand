<?php

namespace App\Models\Post;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostStatus extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "post_status";
    protected $fillable = [ "post_id", "status_id", "permission_type_id","other_id" ];
    // other_id  از این جدول باید حذف گردد.
    public $timestamps = false;

    public static function getAllowedStatus($permission_type_id = 1, $status_type_id = false ) {

        //$post_ids    = \Auth::user()->posts->pluck( "post_id" );
        $post_ids    = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids" );
        $statusIds   =
            PostStatus::
            join( "status", "status_id", "status.id" )->
            whereIn( "post_id", $post_ids )->
            /** در صورتی که نوع دسترسی مشخص شده باشد */
            when( $permission_type_id, function ( $query ) use ( $permission_type_id ) {
                return $query->where(
                    [
                        "permission_type_id" => $permission_type_id,
                    ]
                );

            } )->
            /** در صورتی که شناسه other_id مشخص شده باشد */
            when( $status_type_id, function ( $query ) use ( $status_type_id ) {
                return $query->where( "status_type_id", $status_type_id );
            } )->
            pluck( "status_id" )->
            toArray();
        $statusIds[] = - 1;

        return $statusIds;
    }
}
