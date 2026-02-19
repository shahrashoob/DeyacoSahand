<?php

namespace App\Models\Post;

use App\Models\Accounting\Contract\Contract;
use App\Models\Accounting\Contract\ContractType;
use App\Models\Contractor\ContractorPost;
use App\Models\Customer\ChannelType;
use App\Models\Customer\ChannelTypePost;
use App\Models\HR\Shift\Shift;
use App\Models\HR\Shift\ShiftDeliveryModule;
use App\Models\HR\User\CooperationType;
use App\Models\HR\User\CooperationTypePermission;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\LineProduct\GoodsKind\GoodsKindPropertyPost;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LinePost;
use App\Models\User;
use App\Models\Utility\Address\Province;
use App\Models\Utility\Address\ProvincePost;
use App\Models\Utility\HR\User\CooperationTypePost;
use App\Models\Utility\Menu\Button;
use App\Models\Utility\Menu\ButtonPost;
use App\Models\Utility\Menu\Menu;
use App\Models\Utility\Menu\MenuPost;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehousePost;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = [
        "caption",
        "chart_id",
        "role_id",
        "parent_id",
        "duties",
        'number_of_contract',
        'daily_salary',
        'name_of_work_medicine_doctor',
        'address_of_work_medicine_doctor',
        'right_to_work',
        'number_of_days_before_termination_of_contract',
        "contract_id",
        "contract_type_id",
        'is_basis_for_calculation_working_hour_on_labor_low',
        'is_basis_for_daily_salary_on_labor_low',
        'is_basis_for_duties_on_the_opinion_of_employer',


        "is_system_supervisor",
        "shift_id",
        "organization_category_id",
        "shift_delivery_module_id",
        "the_worker_has_permission_to_leave_after_entering",
        "the_worker_has_permission_to_entering_from_static_ip",

        "emergency_leave_number_in_year",
        "marriage_leave_time_in_year",
        "death_of_relatives_leave_time_in_year",
        "allowed_earlier_time_for_entry",
        "allowed_delay_time_for_entry",
        "allowed_earlier_time_for_exit",
        "allowed_delay_time_for_exit",
        "should_complete_the_duration_of_operation",

        "for_leave_a_few_top_levels_must_confirm",
        "for_leave_a_few_top_levels_must_confirm_time",
        "for_leave_a_few_top_levels_must_confirm_time_level",
        "for_leave_required_to_replace_person",
        "for_leave_number_of_register_after_tacking",

        "for_work_overtime_a_few_top_levels_must_confirm",
        "for_work_overtime_a_few_top_levels_must_confirm_time",
        "for_work_overtime_a_few_top_levels_must_confirm_time_level",
        "for_worker_overtime_number_of_register_after_tacking",
        "for_work_overtime_automatic_number",

        "for_mission_a_few_top_levels_must_confirm",
        "for_mission_a_few_top_levels_must_confirm_time",
        "for_mission_a_few_top_levels_must_confirm_time_level",
        "for_mission_number_of_register_after_tacking_to_posts",

        "for_replacement_a_few_top_levels_must_confirm",
        "for_replacement_a_few_top_levels_must_confirm_time",
        "for_replacement_a_few_top_levels_must_confirm_time_level",

        "allow_enter_gross_weight_by_worker_to_posts",

        "active_status_id",
        "max_person_number_in_shift_work",
        "min_person_number_in_shift_work",
        "allow_show_post_in_employment_register",
        "max_device_allow_for_login",
        "allow_show_result_of_selection",
        'does_it_have_shift_work',

        "traffic_notification_for_post_id"
    ];


    public function organization_category()
    {
        return $this->belongsTo(OrganizationCategory::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public static function InvalidPost($list=[])
    {
        $invalid_list=[ 1100, 1200, 1300];

        $invalid_list = array_merge($invalid_list, $list);
        return $invalid_list;
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function shift_delivery_module()
    {
        return $this->belongsTo(ShiftDeliveryModule::class);
    }

    public function parent()
    {
        return $this->belongsTo(Post::class, "parent_id", "id");
    }

    public function menu_permission()
    {
        return $this->belongsToMany(Menu::class);
    }

    public function post_document_types()
    {
        return $this->hasMany(PostDocumentType::class, 'post_id');
    }

    public function post_contract_types()
    {
        return $this->hasMany(PostContractType::class, 'post_id');
    }

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function post_document_receive_step_confirms()
    {
        return $this->hasMany(PostDocumentReceiveStepConfirm::class, 'post_id');
    }

    public function permission()
    {
        1 / 0;
//        $worker         = Worker::find( Auth::id() );
//        $post_entry_row = $this->post_entry_status()->where( "status_id", $worker->status_id ?? 0 )->first();
//        if ($post_entry_row && $post_entry_row->allow_show_personal_menu && $post_entry_row->allow_show_all_menu ) {
//
//            if( !$post_entry_row->allow_filter_menu){ // فیلتر دسترسی ها با توجه به زمان
//               return  $this->hasMany( MenuPost::class );
//           }
//           else{
//               $shift_work_day_start = ShiftWorkDay::where( [
//                   "shift_id"      => $post_user->post->shift_id,
//                   "shift_work_id" => $post_user->shift_work_id
//               ] )->
//               where( "start_datetime", ">", $start_datetime )->
//               orderBy( "start_datetime" )->first();
//           }
//        } elseif ($post_entry_row && $post_entry_row->allow_show_personal_menu && ! $post_entry_row->allow_show_all_menu ) {
//            return $this->hasMany( MenuPost::class )->where( "menu_id", 515 );// پرسنلی
//        } else {
//            return $this->hasMany( MenuPost::class )->where( "menu_id", 0 );// منو ها خالی باشد
//        }

    }

    public function line_permission()
    {
        return $this->belongsToMany(Line::class);
    }

    public function warehouse_permission()
    {
        return $this->belongsToMany(Warehouse::class);
    }

    public function channel_type_permission()
    {
        return $this->belongsToMany(ChannelType::class);
    }

    public function province_permission()
    {
        return $this->belongsToMany(Province::class);
    }

    public function status_permission()
    {
        return $this->belongsToMany(Status::class);
    }

    public function button_permission()
    {
        return $this->belongsToMany(Button::class);
    }

    public function post_entry_status()
    {
        return $this->hasMany(PostEntryStatus::class);
    }

    public function has_menu_permission($menu_id)
    {
        return MenuPost::where(["post_id" => $this->id, "menu_id" => $menu_id])->exists();
    }

    public function has_goods_kind_permission($goods_kind_id)
    {
        return GoodsKindPost::where(["post_id" => $this->id, "goods_kind_id" => $goods_kind_id])->exists();
    }

    public function has_goods_kind_property_permission($goods_kind_property_id)
    {
        return GoodsKindPropertyPost::where([
            "post_id" => $this->id,
            "goods_kind_property_id" => $goods_kind_property_id
        ])->exists();
    }

    public function has_line_permission($line_id)
    {
        return LinePost::where(["post_id" => $this->id, "line_id" => $line_id])->exists();
    }

    public function get_lines_id_permission()
    {
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", \Auth::user());
        // $post_ids = \Auth::user()->posts->pluck( "post_id" );

        return LinePost::whereIn("post_id", $post_ids)->pluck('line_id')->toArray();
    }

    public static function get_warehouse_id_permission()
    {
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", \Auth::user());
        // $post_ids = \Auth::user()->posts->pluck( "post_id" );

        return WarehousePost::whereIn("post_id", $post_ids)->pluck('warehouse_id')->toArray();
    }

    public static function GetAllLinePermission($post_ids)
    {
        return LinePost::whereIn("post_id", $post_ids)->pluck('line_id')->toArray();
    }

    public static function GetAllStatusPermission()
    {
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids", \Auth::user());
        // $post_ids = \Auth::user()->posts->pluck( "post_id" );

        return PostStatus::whereIn("post_id", $post_ids)->pluck('status_id')->toArray();
    }

    public function has_order_status_permission($status_id, $permission_type_id = false, $other_id = false)
    {
        return PostStatus::where(["post_id" => $this->id, "status_id" => $status_id])->
        /** در صورتی که نوع دسترسی مشخص شده باشد */
        when($permission_type_id, function ($query) use ($permission_type_id, $other_id) {
            return $query->where(
                [
                    "permission_type_id" => $permission_type_id,
                    "other_id" => $other_id
                ]
            );

        })->
        exists();
    }

    public function get_order_id_permission()
    {
        // return LinePost::where(["post_id" => $this->id])->pluck('line_id')->toArray();
    }


    public function has_cooperation_type($cooperation_type_id, $post_id = false)
    {
        return PostCooperationType::where([
            "post_id" => $post_id ? $post_id : $this->id,
            "cooperation_type_id" => $cooperation_type_id
        ])->exists();

    }

    public function has_warehouse_permission($warehouse_id)
    {
        return WarehousePost::where(["post_id" => $this->id, "warehouse_id" => $warehouse_id])->exists();
    }


    public function has_channel_type_permission($channel_type_id)
    {
        return ChannelTypePost::where(["post_id" => $this->id, "channel_type_id" => $channel_type_id])->exists();

    }


    public function has_contractor_permission($contractor_id)
    {
        return ContractorPost::where(["post_id" => $this->id, "contractor_id" => $contractor_id])->exists();
    }

    public function has_province_permission($channel_type_id)
    {
        return ProvincePost::where(["post_id" => $this->id, "province_id" => $channel_type_id])->exists();

    }

    public function has_cooperation_status_permission($cooperation_type_id)
    {
        return CooperationTypePermission::where([
            "post_id" => $this->id,
            "cooperation_type_id" => $cooperation_type_id
        ])->exists();

    }

    public function get_cooperation_status_permission()
    {
        return CooperationTypePermission::where(["post_id" => $this->id])->pluck('cooperation_type_id')->toArray();

    }

    public function has_button_permission($button_id, $permission_type_id = false, $other_id = false)
    {
        return ButtonPost::where(["post_id" => $this->id, "button_id" => $button_id])->
        /** در صورتی که نوع دسترسی مشخص شده باشد */
        when($permission_type_id, function ($query) use ($permission_type_id, $other_id) {
            return $query->where(
                [
                    "permission_type_id" => $permission_type_id,
                    "other_id" => $other_id
                ]
            );

        })->exists();

    }

    public function has_machine_permission($machine_id)
    {
        return LinePost::where(["post_id" => $this->id, "machine_id" => $machine_id])->exists();

    }

    public function worker()
    {
        return $this->belongsToMany(User::class)->orderBy("shift_work_id")->withPivot("shift_work_id");
    }

    public function cooperation_types()
    {
        return $this->belongsToMany(CooperationType::class);
    }

    public static function canHasParent(Post $post, $parent_id)
    {
//echo "<br/>".$post->id." parent_id".$parent_id;
        if ($post && $post->parent_id == 0) {
            return true;
        } elseif (($post && $post->id == $parent_id) || !$post) {
            return false;
        } else {
            if ($post->parent) {
                return true;
            }

            return Post::canHasParent($post->parent, $parent_id);
        }
    }


    public function has_post_replace($post_id)
    {
        return PostReplace::where([
            "post_id" => $this->id,
            "replace_post_id" => $post_id
        ])->exists();
    }

    public function post_replace()
    {
        return $this->hasMany(PostReplace::class);
    }

    public function has_post_entry_permission($status_id, $type)
    {
        $row = $this->post_entry_status()->where("status_id", $status_id)->first();
        if (!$row) {
            return false;
        }
        switch ($type) {
            case "allow_show_personal_menu":
                return $row->allow_show_personal_menu;

            case "allow_show_all_menu":
                return $row->allow_show_all_menu;
            case "allow_filter_menu":
                return $row->allow_filter_menu;
        }

    }

    public function contract_type()
    {
        return $this->belongsTo(ContractType::class);
    }

    public static function ShowPostInIc($post_id, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . "/api/hr/post/",
            'headers' => [
            ],
            'query' => [
                'token' => $token,
                'post_id' => $post_id,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "show_post",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function SearchPostInIc($search, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . "/api/hr/post/",
            'headers' => [
            ],
            'query' => [
                'token' => $token,
                'search' => $search
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "search_post",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

}
