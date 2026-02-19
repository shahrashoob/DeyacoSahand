<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Warehouse\ProductionWarehouse\DashboardController;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\GoodsKindProcess\Fabric_Raw\Fabric_Raw;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use function back;
use function event;
use function redirect;
use function view;

class GeneralLogController extends Controller {
    var $view_path = "goods_kind_process.general.machine.log.";
    var $route_path;
    var $dashboard_route;
    public function index(Request $request,Machine $machine ) {

        if ($request->isMethod('post')) {
            $search = $request->search;
            $user_id = $request->user_id;
            $operator_id = $request->operator_id;
            $production_status_id = $request->production_status_id;
            $machine_event_type_id = $request->machine_event_type_id;
            $from_datetime = $request->form_datetime;
            $to_datetime = $request->to_datetime;

        } else {
            $search = session("search_machine_log");
            $user_id = session("search_machine_log_user_id");
            $operator_id = session("search_machine_log_operator_id");
            $production_status_id = session("search_machine_log_production_status_id");
            $machine_event_type_id = session("search_machine_log_machine_event_type_id");
            $from_datetime = session("search_machine_log_from_datetime");
            $to_datetime = session("search_machine_log_to_datetime");

        }
        session([
            "search_machine_log" => $search,
            "search_machine_log_user_id" => $user_id,
            "search_machine_log_operator_id" => $operator_id,
            "search_machine_log_production_status_id" => $production_status_id,
            "search_machine_log_machine_event_type_id" => $machine_event_type_id,
            "search_machine_log_from_datetime" => $from_datetime,
            "search_machine_log_to_datetime" => $to_datetime,
        ]);

        $user_ids = MachineLog::where("machine_id", $machine->id)->groupBy("user_id")->pluck("user_id")->toArray();
        $operator_ids = MachineLog::where("machine_id", $machine->id)->groupBy("operator_id")->pluck("operator_id")->toArray();
        $production_status_ids = MachineLog::where("machine_id", $machine->id)->groupBy("production_status_id")->pluck("production_status_id")->toArray();
        $machine_event_type_ids = MachineLog::where("machine_id", $machine->id)->groupBy("machine_event_type_id")->pluck("machine_event_type_id")->toArray();

        $user_option = Option::get("worker_in_ids", $user_id, null, $user_ids);
        $operator_option = Option::get("worker_in_ids", $operator_id, null, $operator_ids);
        $production_status_option = Option::get("status_in_ids", $production_status_id, null, $production_status_ids);
        $machine_event_type_option = Option::get("machine_event_type_in_ids", $machine_event_type_id, null, $machine_event_type_ids);


        $list = MachineLog::
        where("machine_id", $machine->id)->
        when($user_id, function ($query) use ($user_id) {
            return $query->where("user_id", $user_id);
        })->
        when($operator_id, function ($query) use ($operator_id) {
            return $query->where("operator_id", $operator_id);
        })->
        when($production_status_id, function ($query) use ($production_status_id) {
            return $query->where("production_status_id", $production_status_id);
        })->
        when($machine_event_type_id, function ($query) use ($machine_event_type_id) {
            return $query->where("machine_event_type_id", $machine_event_type_id);
        })->
        when($from_datetime, function ($query) use ($from_datetime) {
            return $query->where("created_at", ">=", $from_datetime);
        })->
        when($to_datetime, function ($query) use ($to_datetime) {
            return $query->where("created_at", "<=", $to_datetime);
        })->
        orderByDesc("id")->
        paginate(50);

        $route_path = $this->route_path;
        $dashboard_route=$this->dashboard_route;
        return view($this->view_path . "index", compact("machine", "list", "route_path","dashboard_route",
            "user_option", "operator_option", "production_status_option", "machine_event_type_option", "from_datetime", "to_datetime"
        ));

    }

    public function view_input_log(Machine $machine, CurrentMachineInput $current_machine_input)
    {
        $list = $current_machine_input->current_machine_input_logs()->paginate();
        return view($this->view_path . "view_log_input", compact("machine", "current_machine_input", "list"));
    }
}
