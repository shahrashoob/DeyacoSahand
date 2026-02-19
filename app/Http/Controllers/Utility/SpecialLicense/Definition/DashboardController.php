<?php

namespace App\Http\Controllers\Utility\SpecialLicense\Definition;

use App\Http\Controllers\Controller;
use App\Models\Utility\Option;
use App\Models\Utility\SpecialLicense\SpecialLicenseType;
use App\Models\Utility\SpecialLicense\SpecialLicenseTypeExpert;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    private $view_path = "utility.special_license.definition.dashboard.";
    private $route_path = "utility.special_license.definition.dashboard.";
    private $max_priority = 5;

    public function index()
    {
        $list = SpecialLicenseType::paginate();

        return view($this->view_path . "index", compact("list"));
    }

    public function edit(SpecialLicenseType $special_license_type)
    {

        $post_option_all = Option::get("posts");
        $committee_option_all = Option::get("committee");
        $floating_post_all = Option::get("floating_post");
        $max_priority = $this->max_priority;
        for ($k = 1; $k <= $max_priority; $k++) {
            $post_option[$k] = $post_option_all;
            $committee_option[$k] = $committee_option_all;
            $floating_post_option[$k] = $floating_post_all;

            $post_ids = SpecialLicenseTypeExpert::
            where("special_license_type_id", $special_license_type->id)->
            whereNotNull("post_id")->
            where("priority_number", $k)->
            pluck("post_id")->
            toArray();
            $committee_ids = SpecialLicenseTypeExpert::
            where("special_license_type_id", $special_license_type->id)->
            whereNotNull("committee_id")->
            where("priority_number", $k)->
            pluck("committee_id")->
            toArray();


            $floating_post_ids = SpecialLicenseTypeExpert::
            where("special_license_type_id", $special_license_type->id)->
            whereNotNull("floating_post_type_id")->
            where("priority_number", $k)->
            pluck("floating_post_type_id")->
            toArray();

            foreach ($post_option[$k]["items"] as &$item) {
                if (in_array($item["value"], $post_ids)) {
                    $item["selected"] = 1;
                }
            }
            foreach ($committee_option[$k]["items"] as &$item) {
                if (in_array($item["value"], $committee_ids)) {
                    $item["selected"] = 1;
                }
            }
            foreach ($floating_post_option[$k]["items"] as &$item) {
                if (in_array($item["value"], $floating_post_ids)) {
                    $item["selected"] = 1;
                }
            }
        }
        return view($this->view_path . "edit", compact("special_license_type", "committee_option", "post_option", "max_priority", "floating_post_option"));
    }

    public function update(Request $request, SpecialLicenseType $special_license_type)
    {

        self::CreateSpecialLicenseTypeExpert($request,$special_license_type,$this->max_priority);

        return redirect()->route($this->route_path . "percent_of_committee", $special_license_type);

    }

    public function percent_of_committee(SpecialLicenseType $special_license_type)
    {
        if ($special_license_type->special_license_type_expert_committee()->count() == 0) {
            return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

        }
        return view($this->view_path . "percent_of_committee", compact("special_license_type"));
    }

    public function submit_percent_of_committee(Request $request, SpecialLicenseType $special_license_type)
    {

        foreach ($special_license_type->special_license_type_expert_committee as $item) {
            $key = "percent_of_committee_" . $item->id;
            $item->min_percent_of_committee = $request->$key;
            $item->save();
        }
        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

    }
    public function edit_status(SpecialLicenseType $special_license_type)
    {
        $status_option = Option::get( "status", $special_license_type->active_status_id, 1100 );
        $sms_status_option = Option::get( "status", $special_license_type->sms_status_id, 1100 );
        return view($this->view_path . "edit_status", compact("special_license_type","sms_status_option",'status_option'));
    }

    public function update_status(Request $request, SpecialLicenseType $special_license_type)
    {

        $special_license_type->active_status_id=$request->active_status_id;
        $special_license_type->sms_status_id=$request->sms_status_id;
        $special_license_type->save();

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

    }
    public static function CreateSpecialLicenseTypeExpert($request,$special_license_type,$max_priority){
        //در این تابع کمیته پست مجوز ذخیره می شود
        $has_error = true;
        $error = "";

        $post_committee_ids["post_ids"] = [];
        $post_committee_ids["committee_ids"] = [];
        $post_committee_ids["floating_post"] = [];
        for ($k = 1; $k <= $max_priority; $k++) {
            $key_post = "post_ids" . $k;
            $key_committee = "committee_ids" . $k;
            $key_floating_post = "floating_post_ids" . $k;
            if (isset($request->$key_post) || isset($request->$key_committee)) {
                $has_error = false;
            }
            if (isset($request->$key_post)) {
                //چک کردن اینکه پست تکراری وارد نکرده باشند
                foreach ($request->$key_post as $post_id) {
                    if (!in_array($post_id, $post_committee_ids["post_ids"])) {
                        $post_committee_ids["post_ids"][] = $post_id;
                    } else {
                        $error = "لطفا هر پست را تنها در یکی از اولویت ها انتخاب کنید.";
                    }
                }
            }

            if (isset($request->$key_committee)) {
                //چک کردن اینکه کمیته تکراری وارد نکرده باشند
                foreach ($request->$key_committee as $committee_id) {
                    if (!in_array($committee_id, $post_committee_ids["committee_ids"])) {
                        $post_committee_ids["committee_ids"][] = $committee_id;
                    } else {
                        $error = "لطفا هر کمیته را تنها در یکی از اولویت ها انتخاب کنید.";
                    }
                }
            }

            if (isset($request->$key_floating_post)) {
                //چک کردن اینکه کمیته تکراری وارد نکرده باشند
                foreach ($request->$key_floating_post as $floating_post) {
                    if (!in_array($floating_post, $post_committee_ids["floating_post"])) {
                        $post_committee_ids["floating_post"][] = $floating_post;
                    } else {
                        $error = "لطفا هر پست شناور را تنها در یکی از اولویت ها انتخاب کنید.";
                    }
                }
            }


        }

        if ($has_error) {
            return back()->withErrors("لطفا حداقل یک اولویت از بین پست ها ویا کمیته ها مشخص کنید.");
        }
        if ($error != "") {
            return back()->withErrors($error);
        }
        $min_percent_of_committee = SpecialLicenseTypeExpert::
        where("special_license_type_id", $special_license_type->id)->
        whereNotNull("committee_id")->
        pluck("min_percent_of_committee", "committee_id")->
        toArray();

        SpecialLicenseTypeExpert::
        where("special_license_type_id", $special_license_type->id)->delete();
        for ($k = 1; $k <= $max_priority; $k++) {
            $key_post = "post_ids" . $k;
            $key_committee = "committee_ids" . $k;
            $key_floating_post = "floating_post_ids" . $k;

            if (isset($request->$key_post)) {
                foreach ($request->$key_post as $item) {
                    SpecialLicenseTypeExpert::create([
                        "special_license_type_id" => $special_license_type->id,
                        "post_id" => $item,
                        "priority_number" => $k
                    ]);
                }
            }

            if (isset($request->$key_committee)) {
                foreach ($request->$key_committee as $item) {
                    SpecialLicenseTypeExpert::create([
                        "special_license_type_id" => $special_license_type->id,
                        "committee_id" => $item,
                        "min_percent_of_committee" => isset($min_percent_of_committee[$item]) ? $min_percent_of_committee[$item] : 100,
                        "priority_number" => $k
                    ]);
                }
            }

            if (isset($request->$key_floating_post)) {
                foreach ($request->$key_floating_post as $item) {
                    SpecialLicenseTypeExpert::create([
                        "special_license_type_id" => $special_license_type->id,
                        "floating_post_type_id" => $item,
                        "priority_number" => $k
                    ]);
                }
            }
        }

    }
}
