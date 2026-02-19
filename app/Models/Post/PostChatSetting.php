<?php

namespace App\Models\Post;

use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Supplier\Supplier;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PostChatSetting extends Model
{
    use HasFactory;

    protected $table = "post_chat_setting";
    protected $fillable = ["post_id", "chat_with_post_id", "chat_with_customers", "chat_with_suppliers", "chat_with_contractors"];

    public static function getUserIds()
    {

        // پست های سازمانی
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;
        $user_ids_customer = [];
        $user_ids_supplier = [];
        $user_ids_contractor = [];
        $post_setting_chat = PostChatSetting::whereIn("post_id", $post_ids)->get();
        foreach ($post_setting_chat as $item) {
            if ($item->chat_with_post_id) {
                $post_ids[] = $item->chat_with_post_id;
            }
            if ($item->chat_with_customers) {
                $user_ids_customer = Customer::pluck("user_id", "user_id")->toArray();
            }
//            if ($item->chat_with_suppliers) {
//                $user_ids_supplier = Supplier::where("active_status_id", 1200)->pluck("user_id", "user_id");
//            }
//            if ($item->chat_with_contractors) {
//                 $user_ids_contractor = Contractor::where("active_status_id", 1200)->pluck("user_id", "user_id");
//            }
        }

        $can_chat_with_post_ids = PostChatSetting::whereIn("post_id", $post_ids)->pluck("chat_with_post_id")->toArray();
        $can_chat_with_post_ids[] = -1;
        $user_ids = PostUser::whereIn("post_id", $can_chat_with_post_ids)->pluck("user_id", "user_id")->toArray();
        $user_ids[] = -1;

        foreach ($user_ids_customer as $id) {
            $user_ids[] = $id;
        }
        foreach ($user_ids_supplier as $id) {
            $user_ids[] = $id;
        }
        foreach ($user_ids_contractor as $id) {
            $user_ids[] = $id;
        }


        return $user_ids;
    }

    public static function CanChat($from_id, $to_id)
    {
        $exists = DB::table("ch_messages")->where([
            "from_id" => $from_id,
            "to_id" => $to_id
        ])->
        count();
        if ($exists) {
            return true;
        }
        $exists = DB::table("ch_messages")->where([
            "from_id" =>$to_id ,
            "to_id" => $from_id
        ])->
        count();
        if ($exists) {
            return true;
        }
        if (in_array($to_id, self::getUserIds())) {
            return true;
        }

        return false;
    }

    public static function UpdateChatSetting($to_id)
    {
        $count = DB::table("ch_messages")->where([
            "to_id" => $to_id,
            "seen" => 0
        ])->count("seen");

        $user = User::where("id", $to_id)->first();
        if ($user) {
            $user->seen_count = $count;
            $user->save();
        }
    }
}
