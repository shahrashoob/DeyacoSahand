<?php

namespace App\Http\Controllers\LineProductStation\Product\ProductCreation;

use App\Events\Product\ProductCreationProcessLogEvent;
use App\Http\Controllers\Controller;
use App\Http\Controllers\LineProductStation\ProductController;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Product\ProductCreation\ProductCreationProcess;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasicInformationRegistrationController extends Controller
{
    //
    public static $info = [
        "route" => "line_product_station.product.product_creation.basic_information_registration.",
        "view" => "line_product_station.product.product_creation.basic_information_registration.",
        "enable_status" => ["002"],
        "priority_number" => 200,
        "button" => ["caption" => "ثبت اطلاعات تکمیلی", "class" => "btn-primary"],
        "button_id" => 5231002,
    ];
    private $view_path;
    private $route_path;
    public $dashboard_path = "line_product_station.product.product_creation.dashboard.";

    public function __construct()
    {
        $this->view_path = BasicInformationRegistrationController::$info["view"];
        $this->route_path = BasicInformationRegistrationController::$info["route"];
    }

    public function index(ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        if (!$product_creation_process->product) {
            // در صورتی که کالای متناظر برای فرم تعریف کالا وجود نداشت یک کالای جدید تعریف و آن را به فرم تخصیص می دهد.
            $product = Product::create([
                "caption" => $product_creation_process->caption,
                "goods_kind_id" => $product_creation_process->goods_kind_id ?? 0,
                "product_service_type_id" => $product_creation_process->product_service_type_id,
                "active_status_id" => 1210 // غیر فعال
            ]);
            $product_creation_process->product_id = $product->id;
            $product_creation_process->save();

            //کد کالای آزمایشی: رسته کالایی - شناسه کالا
            $product->code = $product_creation_process->goods_kind->code . "/" . $product->id;
            $product->save();

            event(new ProductCreationProcessLogEvent($product_creation_process, 5231004));
        } else {
            $product = $product_creation_process->product;
        }

//        $unit_option        = Option::get( "unit", $product->unit_id );
//        $sub_unit_option    = Option::get( "unit", $product->sub_unit_id, 2 );
//        $sub_unit2_option   = Option::get( "unit", $product->sub_unit2_id, 2 );
//        $goods_type_option  = Option::get( "goods_type", $product->goods_type_id );
        $supply_type_option = Option::get("supply_type", $product->supply_type_id);
        $unit_option = Option::get("unit", $product->unit_id, $product->goods_kind_id, [], "default_unit_ids");
        $sub_unit_option = Option::get("unit", $product->sub_unit_id, $product->goods_kind_id, [], "default_sub_unit_ids");
        $sub_unit2_option = Option::get("unit", $product->sub_unit2_id, $product->goods_kind_id, [], "default_sub_unit2_ids");
        $goods_type_option = Option::get("goods_type", $product->goods_type_id, $product->goods_kind_id, [], "default_goods_type_ids");

        $service_id_in_employer_system_option = Option::get("product_service_active", $product->service_id_in_employer_system);

        $unit_of_measure_type_in_production_option = Option::get("unit_of_measure_type", $product->unit_of_measure_type_id_in_production,$product->goods_kind_id,"default_unit_of_measure_type_ids_in_production");
        $unit_of_measure_type_in_sale_option = Option::get("unit_of_measure_type", $product->unit_of_measure_type_id_in_sale,$product->goods_kind_id,"default_unit_of_measure_type_ids_in_sale");

        return view($this->view_path . "index", compact("product_creation_process", "product",
            "unit_option", "sub_unit_option", "sub_unit2_option",
            "goods_type_option",
            "supply_type_option", "service_id_in_employer_system_option","unit_of_measure_type_in_production_option","unit_of_measure_type_in_sale_option"
        ));
    }

    public function submit(Request $request, ProductCreationProcess $product_creation_process)
    {

        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }
        $result_unit =self:: checkUnit($request);
        if (!$result_unit["result"]) {
            return back()->withErrors($result_unit["error"]);
        }

        if ($request["supply_type_id"] == 3 && !$request["service_id_in_employer_system"]) {
            return back()->withErrors("لطفا کد خدمت در سامانه کارفرما را انتخاب نمایید.");
        }
        $request["weight"] = $request->predictive_weight;

        if(!isset($request["sub_unit2_id"]) || !$request["sub_unit2_id"]){
            $request["frame_ratio_unit2"] = null;
        }
        $product_creation_process->product->update($request->all());

        /********* Next Status ************/
        $result_next_status = ProductCreationProcess::GetNextStatus(self::$info["button_id"], $product_creation_process);
        if (!$result_next_status["result"]) {
            return back()->withErrors($result_next_status["error"]);
        }
        $product_creation_process->status_id = $result_next_status["status_id"];
        $product_creation_process->save();
        /********* End Next Status **********/

        event(new ProductCreationProcessLogEvent($product_creation_process, 5231005, "",));

        return redirect()->route($this->dashboard_path . "view", $product_creation_process)->with(["اطلاعات با موفقیت ثبت گردید"]);


    }
    public static function checkUnit(Request $request)
    {
        if (
            $request->unit_id == $request->sub_unit_id ||
            ($request->sub_unit_id == $request->sub_unit2_id && $request->sub_unit_id != 0) ||
            $request->sub_unit2_id == $request->unit_id
        ) {
            return ["result" => false, "error" => "واحد های اصلی و فرعی نمی توانند مشابه باشند."];
        }

        return ["result" => true];
    }
    public function copy_form_other(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }

        if($product_creation_process->product->unit_id != 0){
            return back()->withErrors("با توجه به اینکه قبلا برای این درخواست یک کالا تعریف  شده است، کپی کالا امکان پذیر نیست.");
        }
        $list=Product::where("goods_kind_id",$product_creation_process->goods_kind_id)->get();
        $product_option = Option::get("product_from_list", 0, 0, $list);
        return view($this->view_path . "copy_form_other", compact("product_creation_process","product_option"));
    }
    public function submit_copy_form_other(Request $request, ProductCreationProcess $product_creation_process)
    {
        $result = $this->checkPermission($product_creation_process);
        if ($result != "") {
            return $result;
        }



        if($product_creation_process->product->unit_id != 0){
            return back()->withErrors("با توجه به اینکه قبلا برای این درخواست یک کالا تعریف  شده است، کپی کالا امکان پذیر نیست.");
        }

        $product = Product::find($request->product_id);

        if (!$product) {
            return back()->withErrors("کالایی که می خواهید از آن کپی کنید در سامانه تعریف نشده است.");
        }

        // اگر نیاز به تکمیل اطلاعات بعضی از گام ها نیست از آن عبور می کند.
        $break_step_list_all=[
            "copy_sale_break"=>5231005,
            "copy_classification_break"=>5231007,
            "copy_product_property_value_break"=>5231016,
            "copy_consumed_break"=>5231003,
            "copy_route_break"=>5231008,
            "copy_route_property_break"=>5231009,
            "copy_bom_break"=>5231010,
            "copy_material_flow_break"=>5231014,
            "copy_product_replace_break"=>5231012,
            "copy_packing_type_break"=>5231015,
            "copy_bom_permutation_break"=>5231011,
            "copy_replace_product_break"=>5231012,
            "copy_waste_break"=>5231013,
            "copy_lot_number_break"=>5231017,
            "copy_warehouse_break"=>5231006,
        ];

        $break_step=[];
        foreach ($break_step_list_all as $key => $value) {


            if(isset($request->$key) && !$request->$key ){
                $break_step[]=$value;
            }
        }

        if(count($break_step)>0){
            event(new ProductCreationProcessLogEvent($product_creation_process, 5231610,json_encode($break_step)));
        }

        ProductController::PostCopyFromOther($request, $product, $product_creation_process->product);
        return redirect()->route($this->route_path."index",$product_creation_process)->with(["success" => "اطلاعات کالا با موفقیت در درخواست طراحی کپی گردید."]);

    }

    public function checkPermission(ProductCreationProcess $product_creation_process)
    {

        $result = DashboardController::checkPermissionConditions($product_creation_process, BasicInformationRegistrationController::$info);
        if (!$result["result"]) {
            return back()->withErrors($result["message"]);
        }

        return "";
    }
}
