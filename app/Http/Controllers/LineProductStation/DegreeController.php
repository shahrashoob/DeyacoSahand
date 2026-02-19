<?php

namespace App\Http\Controllers\LineProductStation;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class DegreeController extends Controller
{
    //
    public function index(GoodsKind $goods_kind){
        $degree_type_master_count= Degree::getCountMasterDegree($goods_kind->id);
        $list = Degree:: where("goods_kind_id",$goods_kind->id)->orderBy("active_status_id")->orderBy("degree_type_id")-> paginate(30);

        return view("line_product_station.degree.index", compact("goods_kind","list","degree_type_master_count"));
        ;
    }


    public function create(GoodsKind $goods_kind){

        $warehouse_option = Option::get("warehouse");
        $degree_type_option=Option::get("degree_type");

        return view("line_product_station.degree.create",compact("degree_type_option","warehouse_option","goods_kind"));
    }
    public function store(Request $request,GoodsKind $goods_kind){
        //چک کردن تکراری
        if($request->caption=="" || Degree::Exists($request->caption,$goods_kind->id)){
            return back()->withErrors("عنوان درجه تکراری است");
        }


        if($request->degree_type_id == 1 && ($request->max_sale_type_id ==1 || $request->percent_of_price_reduction!=0)){
            return back()->withErrors("در صورتی که نوع درجه را 'اصلی' انتخاب کنید، باید حداکثر میزان فروش 'نامحدود' و  درصد کاهش قیمت 0 باشد.");
        }
        if(     Degree::getCountMasterDegree($goods_kind->id)==0 && $request->degree_type_id==2 ){
            return back()->withErrors("لطفا ابتدا یک درجه با نوع اصلی تعریف کنید.");

        }

        $degree=Degree::create($request->all());
        $degree->goods_kind_id=$goods_kind->id;
        $degree->save();

        return redirect()->route("line_product_station.degree.index",$goods_kind)->with(["success" => "درجه با موفقیت اضافه شد"]);

    }


    public function edit(GoodsKind $goods_kind,Degree $degree){

        $warehouse_option = Option::get("warehouse",$degree->warehouse_id);
        $degree_type_option=Option::get("degree_type",$degree->degree_type_id);
        $active_status_option=Option::get("active_status",$degree->active_status_id);
        return view("line_product_station.degree.edit",compact("degree","goods_kind","warehouse_option","degree_type_option","active_status_option"));

    }
    public function update(Request $request,GoodsKind $goods_kind,Degree $degree){

        if($request->caption=="" || Degree::Exists($request->caption,$goods_kind->id,$degree->id)){
            return back()->withErrors("عنوان درجه تکراری است");
        }

        if($request->degree_type_id == 1 && ($request->max_sale_type_id ==1 || $request->percent_of_price_reduction!=0)){
            return back()->withErrors("در صورتی که نوع درجه را 'اصلی' انتخاب کنید، باید حداکثر میزان فروش 'نامحدود' و  درصد کاهش قیمت 0 باشد.");
        }

        $degree->update($request->all());

        return redirect()->route("line_product_station.degree.index",$goods_kind)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }
}
