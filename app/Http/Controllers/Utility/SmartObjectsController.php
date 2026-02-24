<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Machine\MachineTypeOutputBandGoodsKind;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use App\Models\Utility\SmartObject;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SmartObjectsController extends Controller
{
    //
    private $view_path = "utility.smart_object.";
    private $route_path = "utility.smart_object.";

    public function index(Request $request)
    {

//        if ( $request->isMethod( 'post' ) ) {
//            $search   = $request->search;
//            $order_by = $request->order_by;
//        } else {
//            $search   = session( "search_cost_center" );
//            $order_by = session( "order_by_cost_center" );
//        }
//        session( [ "search_cost_center" => $search, "order_by_cost_center" => $order_by ] );


        $list = SmartObject::paginate(20);

        $order_by_Option = null;
//        Option::OrderBy( "public", $order_by );
        $search = "";

        return view($this->view_path . "index", compact("list", "search", "order_by_Option"));
    }

    public function create()
    {
        $status_option = Option::get("status", 0, 1100);
        $smart_object_type_option = Option::get("smart_object_type", 0);
        $smart_object = new SmartObject();

        return view($this->view_path . "create", compact("smart_object", "status_option","smart_object_type_option"));
    }

    public function store(Request $request)
    {

        if ($request->caption == "" || SmartObject::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان شیئ  تکراری است");
        }
        $smart_object = SmartObject::create($request->all());

        $smart_object->updateToken();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک شیء با موفقیت اضافه شد"]);

    }

    public function edit(SmartObject $smart_object)
    {
        $status_option = Option::get("status", $smart_object->status_id, 1100);
        $smart_object_type_option = Option::get("smart_object_type", $smart_object->smart_object_type_id);
        return view($this->view_path . "edit", compact("status_option", "smart_object","smart_object_type_option"));

    }

    public function update(Request $request, SmartObject $smart_object)
    {
        if ($request->caption == "" || $smart_object::ExistsCode($request->caption, $smart_object->id)) {
            return back()->withErrors("عنوان تکراری است");
        }

        if ($request->email && in_array($smart_object->smart_object_type_id,[1])) {

            if (Worker::where("email", $request->email)->where("id", "!=", $smart_object->user_id)->exists()) {
                return back()->withErrors("نام کاربری تکراری است.");
            }


            if ($smart_object->worker) {
                $smart_object->worker->email = $request->email;
            } else {
                $worker = Worker::create([
                    "email" => $request->email,
                    "user_type_id" => 3,
                    "cooperation_type_id" => 5
                ]);
                $smart_object->user_id = $worker->id;
                $smart_object->save();
                $smart_object = SmartObject::find($smart_object->id);
            }
            if ($request->password != "********") {
                $smart_object->worker->password = Hash::make($request->password);
            }


            $smart_object->worker->firstname = $request->caption;
            $smart_object->worker->lastname = "";

            $smart_object->worker->save();
        }


        $smart_object->update($request->all());


        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function destroy(SmartObject $smart_object)
    {
        if (
            MachineTypeOutputBandGoodsKind::where("smart_object_id_for_unit", $smart_object->id)->exists() ||
            MachineTypeOutputBandGoodsKind::where("smart_object_id_for_sub_unit", $smart_object->id)->exists() ||
            MachineTypeOutputBandGoodsKind::where("smart_object_id_for_sub_unit2", $smart_object->id)->exists()
        ) {
            return back()->withErrors("به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد");
        }
        $smart_object->delete();

        return redirect()->route($this->route_path . "index")->with(["success" => "یک شیء با موفقیت حذف گردید"]);

    }

    public function check_connection(SmartObject $smart_object)
    {
        $result = SmartObject::getContour($smart_object);
        if ($result["result"]) {
            return back()->with(["success" => "اتصال به " . $smart_object->caption . " برقرار می باشد." . "<br/>" . json_encode( $result["data"])]);
        } else {
            return back()->withErrors($result["error"]);
        }
    }

    public function updateContourAPI(Request $request)
    {

        $smart_object = SmartObject::where("token", $request->token)->first();
        if (!$smart_object) {
            return " شیء یافت نشد.";
        }

        $smart_object->contour = $request->contour ?? 1;
        $smart_object->contour1 = $request->contour1 ?? 0;
        $smart_object->contour2 = $request->contour2 ?? 0;
        $smart_object->contour3 = $request->contour3 ?? 0;
        $smart_object->contour4 = $request->contour4 ?? 0;

        $smart_object->save();
    }

    public function setting()
    {
        $values = Setting::getValues();

        return view($this->view_path . "setting", compact("values"));

    }

    public function submit_setting(Request $request)
    {
        $setting = Setting::get();
        foreach ($setting as $item) {
            $key = $item->key;

            if (isset($request->$key)) {
                $item->string_value = $request->$key;
                $item->integer_value = $request->$key;
                $item->double_value = $request->$key;
            }
            $item->save();


        }

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره گردید"]);

    }
}
