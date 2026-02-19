<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionAlgorithmType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class GoodsKindController extends Controller
{
    private $view_path = "line_product_station.goods_kind.";
    private $route_path = "line_product_station.goods_kind.";

    public function index()
    {
        $list = GoodsKind::
        orderBy("active_status_id")->
        paginate(30);

        return view($this->view_path . "index", compact("list"));
    }

    public function create()
    {
        $goods_kind = new GoodsKind();

        $production_algorithm_type_option = Option::get("production_algorithm_type");
        $post_option = Option::get("posts");

        return view($this->view_path . "create", compact("goods_kind", "production_algorithm_type_option", "post_option"));
    }

    public function store(Request $request)
    {
        if ($request->caption == "" || GoodsKind::ExistsCode($request->caption)) {
            return back()->withErrors("عنوان تکراری است");
        }
        $request["required_parent_production"] = $request->required_parent_production ? 1 : 0;
        $request["checking_compatibility_grade_in_delivery"] = $request->checking_compatibility_grade_in_delivery ? 1 : 0;
        $request["possibility_of_issuing_a_production_manually"] = $request->possibility_of_issuing_a_production_manually ? 1 : 0;
        $request["checking_carrier_at_delivery_of_product"] = $request->checking_carrier_at_delivery_of_product ? 1 : 0;
        $request["possibility_of_issuing_a_sample_production_manually"] = $request->possibility_of_issuing_a_sample_production_manually ? 1 : 0;
        $request["record_entry_into_warehouse_manually"] = $request->record_entry_into_warehouse_manually ? 1 : 0;
        $request["record_out_of_warehouse_manually"] = $request->record_out_of_warehouse_manually ? 1 : 0;
        $request["allow_show_sub_amount_in_exit_forms"] = $request->allow_show_sub_amount_in_exit_forms ? 1 : 0;

        GoodsKind::create($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "یک نوع جنس کالا با موفقیت اضافه شد"]);

    }

    public function edit(GoodsKind $goods_kind)
    {

        $property1_option = Option::get("main_property_by_goods_kind", $goods_kind->property1_id, $goods_kind->id);
        $property2_option = Option::get("main_property_by_goods_kind", $goods_kind->property2_id, $goods_kind->id);
        $property3_option = Option::get("main_property_by_goods_kind", $goods_kind->property3_id, $goods_kind->id);
        $production_algorithm_type_option = Option::get("production_algorithm_type", $goods_kind->production_algorithm_type_id);
        $status_option = Option::get("active_status", $goods_kind->active_status->id ?? 0,);
        $post_option = Option::get("posts", $goods_kind->send_sms_in_create_allocation_machine_to_post_id1);
        return view($this->view_path . "edit", compact("goods_kind", "production_algorithm_type_option", "status_option", "post_option", "property1_option", "property2_option", "property3_option"));

    }

    public function update(Request $request, GoodsKind $goods_kind)
    {
        if ($request->caption == "" || $goods_kind::ExistsCode($request->caption, $goods_kind->id)) {
            return back()->withErrors("کد تکراری است");
        }

        $request["required_parent_production"] = $request->required_parent_production ? 1 : 0;
        $request["checking_compatibility_grade_in_delivery"] = $request->checking_compatibility_grade_in_delivery ? 1 : 0;
        $request["possibility_of_issuing_a_production_manually"] = $request->possibility_of_issuing_a_production_manually ? 1 : 0;
        $request["checking_carrier_at_delivery_of_product"] = $request->checking_carrier_at_delivery_of_product ? 1 : 0;
        $request["possibility_of_issuing_a_sample_production_manually"] = $request->possibility_of_issuing_a_sample_production_manually ? 1 : 0;
        $request["record_entry_into_warehouse_manually"] = $request->record_entry_into_warehouse_manually ? 1 : 0;
        $request["record_out_of_warehouse_manually"] = $request->record_out_of_warehouse_manually ? 1 : 0;
        $request["allow_show_sub_amount_in_exit_forms"] = $request->allow_show_sub_amount_in_exit_forms ? 1 : 0;
        $request["allow_select_partial_of_packing_in_output"] = $request->allow_select_partial_of_packing_in_output ? 1 : 0;

        $goods_kind->update($request->all());
        GoodsKind::UpdateProperty($goods_kind, null, false);

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }

    public function packing_type_list(GoodsKind $goods_kind)
    {

        $packing_type_option = Option::get("packing_type");

        return view($this->view_path . "packing_type.index", compact("goods_kind", "packing_type_option"));
    }

    public function packing_type_add_store(Request $request, GoodsKind $goods_kind)
    {

        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return back()->withErrors("نوع بسته بندی معتبر نمی باشد.");
        }
        $exists = GoodsKind\GoodsKindPackingType::where([
            "goods_kind_id" => $goods_kind->id,
            "packing_type_id" => $request->packing_type_id
        ])->exists();
        if ($exists) {
            return back()->withErrors("این نوع بسته بندی قبلا به رسته کالایی اضافه شده است.");
        }
        GoodsKind\GoodsKindPackingType:: create([
            "goods_kind_id" => $goods_kind->id,
            "packing_type_id" => $request->packing_type_id
        ]);

        return redirect()->back()->with(["success" => "بسته بندی با موفقیت اضافه شد"]);
    }

    public function packing_type_delete(GoodsKind $goods_kind, PackingType $packing_type)
    {

        GoodsKind\GoodsKindPackingType:: where([
            "goods_kind_id" => $goods_kind->id,
            "packing_type_id" => $packing_type->id
        ])->delete();

        return redirect()->back()->with(["success" => "بسته بندی با موفقیت حذف شد"]);

    }


    public function production_form_status(GoodsKind $goods_kind)
    {

        return view($this->view_path . "production_form_status.index", compact("goods_kind"));
    }

    public function production_waiting_status(GoodsKind $goods_kind)
    {

        return view($this->view_path . "production_waiting_status.index", compact("goods_kind"));
    }
    /***
     * Degree Edit
     */
//    public function edit_degree( Product $product, $mode = "view" ) {
//        return view( "line_product_station.product.edit_degree", compact( "product", "mode" ) );
//    }
//
//    public function store_degree( Request $request, Product $product ) {
//
//        $result = Degree::Exists( $request->caption, $product->id );
//        if ( $result ) {
//            return back()->withErrors( "عنوان درجه تکراری است" );
//        }
//
//        $degree             = new Degree();
//        $degree->caption    = $request->caption;
//        $degree->product_id = $product->id;
//        $degree->save();
//
//        return back()->with( [ "success" => "درجه با موفقیت اضافه گردید" ] );
//    }
//
//    public function update_degree( Request $request, Product $product ) {
////        return $request->data;
//        foreach ( $request->data["value"] as $key => $value ) {
//            $degree = Degree::find( $key );
//            if ( $degree ) {
//                $degree->caption = $value;
//                $degree->save();
//            }
//        }
//
//        return redirect()->route( "line_product_station.product.edit_degree", $product )->with( [ "success" => "اطلاعات با موفقتی ذخیره گردید" ] );
//    }


}
