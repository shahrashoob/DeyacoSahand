<?php

namespace App\Http\Controllers\Accounting\Definition;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function back;
use function redirect;
use function session;
use function view;

class CostCenterController extends Controller
{
    //
    private $view_path = "accounting.definition.cost_center.";
    private $route_path = "accounting.definition.cost_center.";

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_cost_center");
            $order_by = session("order_by_cost_center");
        }
        session(["search_cost_center" => $search, "order_by_cost_center" => $order_by]);


        $list = CostCenter::
        where("code", "like", "%" . $search . "%")->
        orWhere("caption", "like", "%" . $search . "%")->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");
            return $query->orderBy($order_by[0], $order_by[1]);
        })->paginate(50);

        $order_by_Option = Option::OrderBy("public", $order_by);

        return view($this->view_path . "index", compact("list", "search", "order_by_Option"));
    }

    public function create()
    {
        $status_option = Option::get("status", 0, 1100);
        $cost_center = new CostCenter();

        return view($this->view_path . "create", compact("cost_center", "status_option"));
    }

    public function store(Request $request)
    {

        $cost_center=self::CreateCostCenter($request);
        if (!$cost_center['result']) {
            return back()->withErrors($cost_center['error']);
        }
        return redirect()->route($this->route_path . "index")->with(["success" => "مرکز هزینه با موفقیت اضافه شد"]);

    }

    public function edit(CostCenter $cost_center)
    {
        $status_option = Option::get("status", $cost_center->id, 1100);

        return view($this->view_path . "edit", compact("status_option", "cost_center"));

    }

    public function update(Request $request, CostCenter $cost_center)
    {
        if ($request->code == "" || $cost_center::ExistsCode($request->code, $cost_center->id)) {
            return back()->withErrors("کد تکراری است");
        }
        $cost_center->update($request->all());

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

    public static function CreateCostCenter($request)
    {
        $cost_center_exist = CostCenter::where('caption', $request->caption)->exists();
        if ($cost_center_exist) {
            return [
                'result' => false,
                'error' => 'عنوان مرکز هزینه تکراری می باشد.',
            ];
        }
        if ($request->code == "" || CostCenter::ExistsCode($request->code)) {
            return [
                'result' => false,
                'error' => 'کد مرکز هزینه تکراری است.',
            ];
        }
        CostCenter::create($request->all());
        return [
            'result' => true,
            'message' => 'کد مرکز هزینه با موفقیت ثبت شد.',
        ];
    }
}
