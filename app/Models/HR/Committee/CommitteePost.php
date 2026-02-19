<?php

namespace App\Models\HR\Committee;

use App\Models\Post\PostUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CommitteePost extends Model
{
    use HasFactory;
    protected $table="committee_post";
    protected $fillable=["post_id","committee_id"];
    public static function GetAllCommitteePermission() {
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime( "post_ids", \Auth::user());

        return CommitteePost::whereIn( "post_id", $post_ids )->pluck( 'committee_id' )->toArray();
    }
}
