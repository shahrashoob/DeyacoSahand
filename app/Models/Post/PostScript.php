<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostScript extends Model {
    use HasFactory;

    protected $table = "post_script";
    protected $fillable = [ "post_id", "script_id", "allow_edit", "allow_view_log" ];

    public static function getAllowedScript( $post_ids = null ) {

        //$post_ids    = \Auth::user()->posts->pluck( "post_id" );
        if ( ! $post_ids ) {
            $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids" );
        }
        $scriptIds   =
            PostScript::
            whereIn( "post_id", $post_ids )->
            pluck( "script_id" )->
            toArray();
        $scriptIds[] = - 1;

        return $scriptIds;
    }
}
