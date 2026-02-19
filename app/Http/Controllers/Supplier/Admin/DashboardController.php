<?php

namespace App\Http\Controllers\Supplier\Admin;

use App\Http\Controllers\Controller;
use App\Models\Form\FormGeneralItem;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\Utility\Option;
use App\Models\Utility\Transport\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    // supplier/admin/dashboard/
    var $view_path = "supplier.admin.dashboard.";
    var $route_path = "supplier.admin.dashboard.";

    public function index(Request $request)
    {
        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_by = $request->order_by;
        } else {
            $search = session("search_supplier");
            $order_by ="allocations.created_at__desc";
        }
        session(["search_supplier" => $search, "order_by_allocation" => $order_by]);

        $order_by ="allocations.created_at__desc";
//        $list = MachineAllocation::whereNotNull("supplier_id")->orderByDesc("id")->paginate();
        $list = MachineAllocation::
        join('suppliers', 'suppliers.id', 'supplier_id')->
        whereNotNull("supplier_id")->
        when($search != "", function ($query) use ($search) {
            $query->where('caption', 'like', '%' . $search . '%')->
            orWhere("allocation_id", "like", "%" . $search . "%");
        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");
            return $query->orderBy('machine_allocation.id', $order_by[1]);
        })->
        with("supplier")->
        select("machine_allocation.*")->
        paginate();

        $order_by_Option = Option::OrderBy("public", $order_by);

        return view($this->view_path . "index", compact("list", 'order_by_Option', 'search'));
    }

    public function view(MachineAllocation $machine_allocation)
    {

        $form_general_items = FormGeneralItem::
        join("machine_allocation", "machine_allocation.id", "machine_allocation_id")->
        where("machine_allocation_id", $machine_allocation->id)->
        select("form_general_item.*")->get();

        // گرفتن لیست بارها
        $forms = FormGeneralItem::
        join("machine_allocation", "machine_allocation.id", "machine_allocation_id")->
        where("machine_allocation_id", $machine_allocation->id)->
        select("form_general_item.*")->pluck("form_id", "form_id");
        $forms[] = -1;

        $transport_list = Transport::join("transport_form", "transports.id", "transport_id")->
        whereIn("form_id", $forms)->
        select("transports.*")->
        get();

//        $machine_allocation_actual_cost_weight_list = Allocation\MachineAllocationActualCost::
//        where("allocation_id", $machine_allocation->allocation_id)->
//        where("status_id", 5105200)-> // در انتظار ثبت اطلاعات مالی
//        get()->keyBy(function ($item) {
//            return $item["product_id"] . "_" . $item["packing_type_id"];
//        });

        return view($this->view_path . "view", compact("form_general_items", "machine_allocation", "transport_list",));
    }
}
