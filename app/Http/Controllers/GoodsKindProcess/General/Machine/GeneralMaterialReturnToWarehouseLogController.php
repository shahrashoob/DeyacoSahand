<?php

namespace App\Http\Controllers\GoodsKindProcess\General\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\PrintQRController;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Allocation\Modification\MachineAllocationModificationForm;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\CurrentMachineInputLog;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Utility\Option;
use App\Models\Utility\SmartObject;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\LineProduct\Machine\Allocation\Modification;
use Illuminate\Support\Facades\Auth;


class GeneralMaterialReturnToWarehouseLogController extends Controller
{
// برگشت مواد اولیه
    var $view_path = "goods_kind_process.general.machine.material_return_to_warehouse_log.";
    var $route_path;
    var $dashboard_route;

    public function __construct()
    {

    }

    public function index($machine)
    {
        $list = Modification\MachineAllocationModification::where(["machine_id" => $machine->id])->
        orderByDesc("id")->paginate();

        $route_path = $this->route_path;
        $dashboard_route = $this->dashboard_route;
        return view($this->view_path . "index", compact(
            "machine", "list", "route_path", "dashboard_route"
        ));
    }

    public function details($machine, Modification\MachineAllocationModification $machine_allocation_modification)
    {

        $allow_show_log = \Auth::user()->posts->first()->checkButtonPermission("production.machine.index.show_consumption_actual_data");

        $route_path = $this->route_path;

        $modification_temp_packing_forms = $machine_allocation_modification->packing_forms()->paginate();

        $post_user = Auth::user()->posts->first();
        $allow_show_cost = $post_user->checkButtonPermission("fabric_raw.packing_form.show_actual_cost");

        return view($this->view_path . "details", compact(
            "machine", "machine_allocation_modification", "allow_show_cost", "route_path", "allow_show_log", "modification_temp_packing_forms"
        ));
    }

    public function print_one_of_packing_form(PackingForm $packing_form_print, $modification_packing_form_id)
    {
        $modification_packing_form = Modification\MachineAllocationModificationPackingForm::find($modification_packing_form_id);
        if (!$modification_packing_form || $modification_packing_form->packing_form_id != $packing_form_print->id) {
            return back()->withErrors("اطلاعات بسته بندی جهت پرینت نادرست است.");
        }
        $modification_form = MachineAllocationModificationForm::where([
            "machine_allocation_modification_id" => $modification_packing_form->machine_allocation_modification_id,
            "product_id" => $modification_packing_form->product_id
        ])->first();

        if (!$modification_form || $modification_form->input_form_status_id != 6021201) {
            return back()->withErrors("با توجه به اینکه برگشت مواد اولیه انجام شده است، امکان پرینت بسته بندی وجود ندارد.");
        }

        if ($modification_packing_form) {


            $unconfirmed_data["weight"] = $modification_packing_form->weight;
            $unconfirmed_data["gross_weight"] = $modification_packing_form->gross_weight;
            $unconfirmed_data["final_amount"] = $modification_packing_form->amount;
            $unconfirmed_data["sub_packing_form_number"] = $modification_packing_form->sub_packing_form_number;

            $packing_form_print->unconfirmed_data = $unconfirmed_data;
        }
        $worker = Worker::find(Auth::id());
        PrintQRController::direct_print($packing_form_print, $worker);
        return back()->with(["success" => "بسته بندی مورد نظر با موفقیت پرینت شد"]);

    }


}
