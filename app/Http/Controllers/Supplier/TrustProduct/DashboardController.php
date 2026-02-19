<?php

namespace App\Http\Controllers\Supplier\TrustProduct;

use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Form;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Order\Order;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    //
    var $route_path = "supplier.trust_product.dashboard.";
    var $view_path = "supplier.trust_product.dashboard.";
    public static $perfix_production_status_code = 7011;

    public function index(Request $request)
    {

        $allowed_status_ids = Status::where("status_type_id", 7011)->pluck("id")->toArray();
//        if ( $request->waiting_status_id != 0 && ! in_array( $request->waiting_status_id, $allowed_status_ids ) ) {
//            return back()->withErrors( "شما اجازه دسترسی به مشاهده کارت های پیمان با وضعیت انتخاب شده را ندارید" );
//        }

        if ($request->isMethod('post')) {
            $search = $request->search;
            $order_search = $request->order_search;
            $order_by = $request->order_by;
            $waiting_status_id = $request->waiting_status_id;

        } else {
            $search = session("search_trust_product_card");
            $order_by = session("order_by_trust_product_card") ?? "production_cards.updated_at__desc";
            $waiting_status_id = session("waiting_status_id_trust_product_card");
            $order_search = session("order_search_trust_product_card");
        }

        session([
            "search_contractor_card" => $search,
            "order_search_trust_product_card" => $order_search,
            "order_by_contractor_card" => $order_by,
            "waiting_status_id_contractor_card" => $waiting_status_id,
        ]);

        // search
        if ($waiting_status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $waiting_status_id;
        }

        $order_ids = [];

        if ($order_search != "") {
            $order_ids = Order::where("code", "like", "%" . $order_search . "%")->
            orWhere("series", "like", "%" . $order_search . "%")->pluck("id");
        }


        $select = [
            "production_cards.number_in_carton",
            "production_cards.product_id",
            "serial",
            "production_cards.number",
            "production_cards.status_id",
            "production_cards.created_at",
            "production_cards.prioriry_id",
            "production_cards.waiting_status_id",
            "production_cards.id as id",
            "production_cards.order_id",
            "production_type_id"
        ];

        $list = Production::join("products", "production_cards.product_id", "products.id")->

        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                return $query->where("serial", "like", "%" . $search . "%")
                    ->orWhere("products.code", "like", "%" . $search . "%")
                    ->orWhere("products.caption", "like", "%" . $search . "%");
            });


        })->

        when($allowed_status_ids != [], function ($query) use ($allowed_status_ids) {

            return $query->where(function ($query) use ($allowed_status_ids) {
                $query->
                //whereIn( "production_cards.status_id", $allowed_status_ids )->
                OrwhereIn("production_cards.waiting_status_id", $allowed_status_ids);
            });

        })->
        when($order_ids != [], function ($query) use ($order_ids) {
            return $query->whereIn("production_cards.order_id", $order_ids);

        })->
        when($order_by != "", function ($query) use ($order_by) {
            $order_by = Str::of($order_by)->explode("__");

            return $query->orderBy($order_by[0], $order_by[1]);

        })->
        with("order")->
        with("order.customer")->
        select($select)->

        addSelect(DB::raw("production_cards.id as production_card_id"))->

        groupBy("production_cards.id");

        $list = $list->paginate(50);


        $order_by_Option = Option::OrderBy("contractor_allocation", $order_by);

        $waiting_status_option = Option::get("contractor_allocation_waiting_status", $waiting_status_id, 7008);

        return view($this->view_path . "index", compact("waiting_status_option", "order_by_Option", "list", "search", "order_by_Option", "order_search"));
    }

    public function view_card(Production $production)
    {

        // Check Permission
        $this->checkPermission($production);

        $allocation_list = MachineAllocation::where("production_id", $production->id)->get();

        return view($this->view_path . "view_card", compact("production", "allocation_list"));
    }

    public function log(MachineAllocation $contractor_allocation)
    {


        $product_request_form_list = ProductRequestForm::where("allocation_id", $contractor_allocation->allocation_id)->orderByDesc("id")->get();

        $contractor_packing_list = MachineAllocationPackingForm::where([
            "contractor_id" => $contractor_allocation->contractor_id,
            "machine_allocation_id" => $contractor_allocation->id
        ])->
        where("status_id", "!=", "7007006")->//معلق
        get();

        $line_product_station = LineProductStation::
        whereNotNull("contractor_operation_id")->
        where("contractor_id", $contractor_allocation->contractor_id)->
        where("product_id", $contractor_allocation->product_id)->
        first();
        // گرفتن اولین BOM
        $bom = BOM::where("product_route_id", $line_product_station->product_route_id)->first();
        $product_bom = [];
        foreach ($bom->items as $item) {
            $line_product_station = LineProductStation::
            where("product_id", $item->material_id)->
            first();
            if (isset($line_product_station)) {
                $product_bom[$item->material->id] = BOM::where("product_route_id", $line_product_station->product_route_id)->first();
            }

        }


        return view($this->view_path . "log", compact("contractor_allocation", "bom", "product_bom", "product_request_form_list", "contractor_packing_list"));
    }


    public function view_form(ContractorAllocation $contractor_allocation, Form $form)
    {

        $contractor = $contractor_allocation->contractor;

        return view($this->view_path . "view_form", compact("contractor_allocation", "form", "contractor"));
    }


    private function checkPermission($production)
    {
        if ($production->product->supply_type_id != 4) {
            return back()->withErrors("کارت از نوع دریافت امانی نمی باشد.");
        }
    }

    public static function checkPermissionConditions(Production $production, $info = false, $all_status = false)
    {
        if ($production->product->supply_type_id != 4) {
            return [
                "result" => false,
                "message" => "کارت از نوع دریافت امانی نمی باشد.",
            ];
        }

        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = DashboardController::$perfix_production_status_code . $value;
            }
            unset($value);
            if (!$all_status && !in_array($production->waiting_status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "message" => "وضعیت کارت تامین جهت عملیات نامعتبر است",
                    "error_type" => "for_waiting_status"
                ];
            }

        }

        return [
            "result" => true,
        ];

    }

}
