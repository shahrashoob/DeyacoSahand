<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionFrom;


use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\GoodsKindProperty;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Ramsey\Uuid\Exception\UnableToBuildUuidException;
use function Symfony\Component\String\b;

class ImplementationPeriodFormController extends Controller
{
    var $view_path = "goods_kind_process.warps.production_form.implementation_period.";
    var $route_path = "warps.production_form.implementation_period_form.";
    var $dashboard_route = "warps.production_form.dashboard.";
    public static $info = [
        "route" => "warps.production_form.implementation_period_form.",
        "enable_status" => [],
        "button" => ["caption" => " فرم تولید چله کشی (ویژه دوره پیاده سازی) ", "class" => "btn-primary"],
        "view_path" => "goods_kind_process.warps.production_form.implementation_period_form.",
        "message" => ["confirm" => "آیا از پایان طراحی اطمینان دارید؟"],
    ];

    // فرم تولید چله کشی ( ویژه دوره پیاده سازی
    public function index()
    {

        $goods_kind = GoodsKind::getByCaptionEn("Warps");
        $product_option = Option::get("product_by_goods_kind", 0, $goods_kind->id);

        $machine_test ["items"] = [
            [
                "id" => "0",
                "text" => "لطفا یک ماشین را انتخاب کنید ",
                "value" => ""
            ],
            [
                "value" => 1,
                "text" => "ماشین 1"
            ],
            [
                "value" => 2,
                "text" => "ماشین 2"
            ]
        ];

        return view($this->view_path . "index", compact("product_option", "machine_test"));
    }

    public function submit(Request $request)
    {
        $product = Product::find($request->product_id);


        return redirect()->route($this->route_path . "complete_form", [$product, $request->machine_warps_id]);

    }

    public function complete_form(Product $product, $machine_warps_id)
    {

        $degree_option = Option::get("degree", 0, $product->goods_kind->id);
        $packing_type_option = Option::get("packing_type", 0, $product->goods_kind_id);


        return view($this->view_path . "complete_form", compact("product", "degree_option", "machine_warps_id", "packing_type_option"));

    }

    public function submit_complete_form(Request $request, Product $product, $machine_warps_id)
    {

        $degree = Degree::find($request->degree_id);
        $result = $product->getWarehouseForForm($degree);
        if (!$result["result"]) {
            return redirect()->route($this->route_path . "index")->withErrors($result["error"]);
        }
        $warehouse_id = $result["warehouse_id"];


        // LotNumber
        if (isset($request->new_lot_number) && !LotNumber::ExistsCode($request->new_lot_number, $product->id)) {
            LotNumber::insert(["code" => $request->new_lot_number, "product_id" => $product->id]);
        }
        $lot_number = null;
        if (LotNumber::ExistsCode($request->lot_number, $product->id)) {
            $lot_number = LotNumber::where(["product_id" => $product->id, "code" => $request->lot_number])->first();
        } else {
            $property = GoodsKindProperty::where("goods_kind_id", $product->goods_kind->id)->get();
            $property_value = GoodsKindPropertyValue::where("product_id", $product->id)->pluck("value", "goods_kind_property_id");

            $degree_option = Option::get("degree", $request->degree_id, $product->goods_kind->id);
            $packing_type_option = Option::get("packing_type", $request->packing_type_id, $product->goods_kind_id);

            return view($this->view_path . "complete_form", compact("product", "property", "property_value", "degree_option", "request", "machine_warps_id", "packing_type_option"));

        }

        // پیدا کردن نوع حامل از روی نوع بسته بندی
        $packing_type = PackingType::find($request->packing_type_id);
        if (!$packing_type) {
            return redirect()->route($this->route_path . "index")->withErrors("نوع بسته بندی معتبر نمی باشد.");
        }

        $first_layer = $packing_type->layers->where("layer_code", 1)->first();
        if (!$first_layer) {
            return redirect()->route($this->route_path . "index")->withErrors("تعریف نوع حامل در  بسته بندی معتبر نمی باشد، لطفا با پشتیبانی تماس بگیرید.");

        }

        $result = Carrier::firstOrCreate($request->carrier_code, $first_layer->carrier_type_id, 5320001, $product->id);
        if (!$result["result"]) {
            return redirect()->route($this->route_path . "complete_form", [
                $product,
                $machine_warps_id
            ])->withErrors($result["message"]);
        }
        $carrier = $result["carrier"];

        if ($carrier->status_id != 5320001) {
            return redirect()->route($this->route_path . "complete_form", [
                $product,
                $machine_warps_id
            ])->withErrors("شماره غلطک وارد شده خالی نیست، لطفا یک شماره غلطک خالی وارد کنید.");
        }

        $carrier->SetStatus(5320004, null, 5320111); //  پر شده در انتظار تحویل به انبار


        // ایجاد فرم بسته بندی
        $packing_form = PackingForm::create([
            "carrier_id" => $carrier->id,
            "form_id" => 0,
            "status_id" => 7007005,// در انتظار تحویل به انبار
            "packing_type_id" => $packing_type->id,
            "degree_id" => $degree->id,
            "warehouse_status_id" => 4202,// بسته خارج انبار است

        ]);
        $packing_form->getCode();

        $packing_form_item = PackingFormItem::create([
            "packing_form_id" => $packing_form->id,
            "amount" => $request->amount,
            "final_amount" => $request->amount,
            "status_id" => 7006003, // بسته بندی شده
            "sub_amount" => $request->sub_amount ?? 0,
            "init_sub_amount" => $request->sub_amount ?? 0,
            "product_id" => $product->id,
            "degree_id" => $degree->id,
            "lot_number_id" => $lot_number->id,
            "band_code" => 1,

        ]);

        $result = PackingForm::UpdateWeight($packing_form);
        if (!$result["result"]) {
            return redirect()->route($this->route_path . "index")->with(["success" => "فرم چله کشی با موفقیت ثبت گردید، وزن خالص و ناخالص به دلیل زیر قابل محاسبه نیست." . "<br/>" . $result["error"]]);

        }
        $packing_form->weight = $result["weight"];
        $packing_form->gross_weight = $result["gross_weight"];
        $packing_form->save();

        return redirect()->route($this->route_path . "index")->with(["success" => "فرم چله کشی با موفقیت ثبت گردید، لطفا با ورود به داشبورد بسته بندی نسبت به تحویل کالا به انبار اقدام نمایید."]);
    }

    public function show_form(Form $form)
    {
        if ($form->status_id != 500000100) {
            return back()->withErrors("این فرم قبلا ثبت شده است.");
        }
        $form_item = $form->item()->first();
        $product = $form_item->product;

        return view($this->view_path . "show_form", compact("form", "product", "form_item"));
    }

    public function submit_form(Request $request, Form $form)
    {
        if ($form->status_id != 500000100) {
            return back()->withErrors("این فرم قبلا ثبت شده است.");
        }
        $form->status_id = 500000400;
        $form->save();
        event(new FormLogEvent($form));

        return redirect()->route($this->dashboard_route . "index")->with(["success" => "فرم با موفقیت ثبت گردید"]);
    }
}
