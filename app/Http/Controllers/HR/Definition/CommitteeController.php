<?php

namespace App\Http\Controllers\HR\Definition;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\HR\Committee\Committee;
use App\Models\HR\Committee\CommitteePost;
use App\Models\Utility\Option;
use App\Models\Utility\SpecialLicense\SpecialLicenseTypeExpert;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CommitteeController extends Controller
{
    //
    private $view_path = "hr.definition.committee.";
    private $route_path = "hr.definition.committee.";

    public function index(Request $request)
    {

        $list = Committee::paginate(50);
        return view($this->view_path . "index", compact("list"));
    }

    public function create()
    {
        $status_option = Option::get("status", 0, 1100);
        $committee = new Committee();
        $post_option = Option::get("posts");

        return view($this->view_path . "create", compact("committee", "status_option", "post_option"));
    }

    public function store(Request $request)
    {
        if ($request->caption == "" || Committee::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان کمیته تکراری/نادرست است");
        }
        if (!isset($request->post_ids) || count($request->post_ids) <= 0) {
            return back()->withErrors("لطفا حداقل یک عضو کمیته را از بین پست ها انتخاب نمایید.");
        }

        $committee = Committee::create($request->all());
        $committee->getCode();
        foreach ($request->post_ids as $post_id) {
            CommitteePost::create(["committee_id" => $committee->id, "post_id" => $post_id]);
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "یک کمیته با موفقیت اضافه شد"]);

    }

    public function edit(Committee $committee)
    {
        $status_option = Option::get("status", $committee->active_status_id, 1100);
        $post_option = Option::get("posts");
        $post_ids = CommitteePost::where("committee_id", $committee->id)->pluck("post_id")->toArray();
        foreach ($post_option["items"] as &$item) {
            if (in_array($item["value"], $post_ids)) {
                $item["selected"] = 1;
            }
        }

        return view($this->view_path . "edit", compact("status_option", "committee", "post_option"));

    }

    public function update(Request $request, Committee $committee)
    {
        if ($request->caption == "" || Committee::ExistsCode($request->caption, $committee->id)) {
            return back()->withErrors("عنوان کمیته تکراری/نادرست است");
        }
        if (!isset($request->post_ids) || count($request->post_ids) <= 0) {
            return back()->withErrors("لطفا حداقل یک عضو کمیته را از بین پست ها انتخاب نمایید.");
        }

        if ($request->active_status_id == 1210) { // غیر فعال
            $list = SpecialLicenseTypeExpert::where("committee_id", $committee->id)->get();
            if (count($list) > 0) {
                return back()->withErrors("با توجه به اینکه کمیته در " .
                    count($list)
                    . " نوع مجوز انتخاب شده است، امکان غیرفعال کردن آن وجود ندارد.");
            }
        }
        $committee->update($request->all());

        CommitteePost::where(["committee_id" => $committee->id])->delete();
        foreach ($request->post_ids as $post_id) {
            CommitteePost::create(["committee_id" => $committee->id, "post_id" => $post_id]);
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function destroy(CostCenter $cost_center)
    {
        if (
            MachineType::where("ic", $cost_center->id)->exists()
        ) {
            return back()->withErrors("به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد");
        }
        $cost_center->delete();
        return redirect()->route($this->route_path . "index")->with(["success" => "یک آیتم با موفقیت حذف گردید"]);

    }

}
