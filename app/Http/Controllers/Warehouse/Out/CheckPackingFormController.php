<?php

namespace App\Http\Controllers\Warehouse\Out;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormPackingType;
use Illuminate\Http\Request;

class CheckPackingFormController extends Controller
{
    // warehouse/out/check_packing_form

    var $view_path = "warehouse.out.check_packing_form.";
    var $route_path = "wh.out.delivery.";
    var $dashboard_path = "wh.out.dashboard.";

    public function index(ProductRequestForm $product_request_form, $page = 1,$dashboard_type="")
    {

        return view($this->view_path . "index", compact("product_request_form","dashboard_type", "page"));
    }

    public function submit(Request $request, ProductRequestForm $product_request_form, $page = 1,$dashboard_type="")
    {
        $code = "DCPK/" . $request->code;
        $list = [];
        $list_warning = [];
        $packing_form = PackingForm::where("code", $code)->first();

        $product_request_form_ids=[$product_request_form->id];
        if($dashboard_type=="customer"){
            $product_request_form_ids = ProductRequestForm::
            where("applicant_type_id", $product_request_form->applicant_type_id)->
            where("applicant_id", $product_request_form->applicant_id)->
            whereIn("product_request_forms.status_id", [7005001, 7005004, 7005008])->
            pluck("product_request_forms.id")->toArray();
            $product_request_form_ids[] = $product_request_form->id;
        }

        if (!$packing_form) {
            $error = ["code" => 101, "alert" => "danger", "message" => "بسته بندی با کد $code در سامانه وجود ندارد. "];
            $list[] = $error;
            return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));
        }

        // بررسی انبار
        if ($packing_form->status_id != 7007003) {
            $error = ["code" => 110, "alert" => "danger", "message" =>
                "وضعیت بسته بندی (<b>" .
                $packing_form->status->caption .
                "</b>) نامعتبر است، فقط وضعیت <b>تحویل شده به انبار</b> برای تحویل مجاز می باشد."
            ];
            $list[] = $error;
            switch ($packing_form->status_id) {
                case 7007017:
                case 7007016:
                case 7007014:
                    $form = Form::
                    join("form_item", "forms.id", "form_id")->
                    join("packing_form_item", "packing_form_item_id", "packing_form_item.id")->
                    where("packing_form_id", $packing_form->id)->
                    where("form_type_id", 0)->
                    where("forms.status_id", 500000200)-> // تایید شده
                    orderBy("forms.id", "desc")->
                    select("forms.code", "forms.id")->
                    first();
                    if ($form) {
                        $product_request_form_form = ProductRequestFormForm::where("form_id", $form->id)->first();
                        $product_request_form_exit = $product_request_form_form->product_request_form ?? null;
                        $error = ["code" => 110, "alert" => "warning", "message" =>
                            "بسته بندی در فرم خروج از انبار " . $form->code .
                            (
                            isset($product_request_form_exit) ? (" و درخواست " . $product_request_form_exit->code . " (" . $product_request_form_exit->applicant->caption . ") ") : ""
                            ) .
                            " از انبار خارج شده است."
                        ];
                        $list[] = $error;
                    }

                    break;

            }
            return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));
        }
        if (!$packing_form->warehouse) {
            $error = ["code" => 111, "alert" => "danger", "message" => "انبار بسته بندی وجود ندارد، لطفا با پشتیبانی تماس بگیرید. "];
            $list[] = $error;
            return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));
        }
        if ($packing_form->warehouse_id != $product_request_form->warehouse_id) {
            $error = ["code" => 112, "alert" => "danger", "message" => "مغایرت انبار: بسته بندی در " .
                "<b>" . $packing_form->warehouse->caption . "</b>" .
                " قرار دارد در حالی که درخواست کالا برای " .
                "<b>" . $product_request_form->warehouse->caption . "</b>" .
                " می باشد. "];
            $list[] = $error;
            return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));
        }
        if ($packing_form->warehouse_status_id != 4201) {
            $error = ["code" => 113, "alert" => "danger", "message" =>
                "این بسته بندی جهت تحویل در درخواست دیگری رزور شده است. " . "<br/>" . "وضعیت رزور:" . $packing_form->GetWarehouseStatusReferenceCode()
            ];
            $list[] = $error;
            return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));
        }

        // نوع بسته بندی
        $packing_type_ids = [$packing_form->packing_type_id ?? -2, $packing_form->packing_type->first_packing_type_id ?? -1];
        $packing_type_allowed_count = ProductRequestFormPackingType::whereIn("product_request_form_id",$product_request_form_ids)->
        whereIn("packing_type_id", $packing_type_ids)->
        count();
        if ($packing_type_allowed_count == 0) {
            $error = ["code" => 120, "alert" => "danger", "message" =>
                "نوع بسته بندی (<b>" .
                (($packing_form->packing_type ? $packing_form->packing_type->fullCaption() : "")
                    . "|" .
                    ($packing_form->packing_type->first_packing_type_id ? $packing_form->packing_type->first_packing_type->fullCaption() : "")) .
                "</b>) جزء انواع بسته بندی مجاز درخواست نمی باشد. "
            ];
            $list[] = $error;
            return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));

        }
        //   return $packing_form->packing_type;

        foreach ($packing_form->items as $packing_form_item) {


            //چک کردن کالا
            $packing_form_item_product_allowed_count = ProductRequestFormPackingType::whereIn("product_request_form_id",$product_request_form_ids)->
            where("product_id", $packing_form_item->product_id)->
            count();
            if ($packing_form_item_product_allowed_count == 0) {
                $error = ["code" => 121, "alert" => "danger", "message" =>
                    "کالای (" . "<b>" .
                    $packing_form_item->product->fullCaption() . "</b>" .
                    ") که در بسته بندی وجود دارد، جزء کالاهای مجاز جهت تحویل نمی باشد،" . "<br/>" .
                    "تنها در صورتی می توان این کالا را تحویل دارد که " .
                    $product_request_form->applicant->fullCaption() .
                    " یک درخواست دیگری برای این کالا ثبت نموده باشد."
                ];
                $list[] = $error;
            }

            //چک کردن بسته بندی
            $packing_type_ids = [$packing_form->packing_type_id ?? -2, $packing_form->packing_type->first_packing_type_id ?? -1];
            $packing_form_item_packing_type_allowed_count = ProductRequestFormPackingType::whereIn("product_request_form_id",$product_request_form_ids)->
            whereIn("packing_type_id", $packing_type_ids)->
            where("product_id", $packing_form_item->product_id)->
            count();
            if ($packing_form_item_packing_type_allowed_count == 0 && $packing_form_item_product_allowed_count!=0) {
                $packing_form_item_product_allowed_list = ProductRequestFormPackingType::whereIn("product_request_form_id",$product_request_form_ids)->
                where("product_id", $packing_form_item->product_id)->
                get();
                $packing_type_list_caption = "";
                foreach ($packing_form_item_product_allowed_list as $item) {
                    $packing_type_list_caption .= $item->packing_type->fullCaption() . "<br/>";
                }
                $error = ["code" => 121, "alert" => "danger", "message" =>
                    "نوع بسته بندی (" . $packing_form->packing_type->fullCaption() . ")" .
                    "برای کالای (" . "<b>" .
                    $packing_form_item->product->fullCaption() . "</b>" .
                    ") مجاز نمی باشد، تنها انواع بسته بندی های زیر مجاز هستند:" . "<br/>" .
                    $packing_type_list_caption

                ];
                $list[] = $error;
            }

            // چک کردن درجه
            if ($packing_form_item_product_allowed_count != 0 && $packing_form_item->product->goods_kind->checking_compatibility_grade_in_delivery) {

                $packing_type_ids = [$packing_form->packing_type_id ?? -2, $packing_form->packing_type->first_packing_type_id ?? -1];
                $packing_form_item_degree_allowed_count = ProductRequestFormPackingType::whereIn("product_request_form_id",$product_request_form_ids)->
                whereIn("packing_type_id", $packing_type_ids)->
                where("product_id", $packing_form_item->product_id)->
                where("degree_id", $packing_form_item->degree_id)->
                count();
                if ($packing_form_item_degree_allowed_count == 0) {
                    $error = ["code" => 120, "alert" => "danger", "message" =>
                        "کالای (" . "<b>" .
                        $packing_form_item->product->fullCaption() . "</b>" .
                        ") با درجه ( <b>" . $packing_form_item->degree->caption . "</b>)" .
                        " که در بسته بندی وجود دارد، جزء کالاهای مجاز جهت تحویل نمی باشد،" .
                        "<br/>" .
                        "تنها در صورتی می توان این کالا را تحویل داد که " .
                        $product_request_form->worker->fullName() .
                        " یک درخواست دیگری برای این کالا ثبت نموده باشد."
                    ];
                    $list[] = $error;
                }

            }
        }

        $has_error = count($list);
        foreach ($list_warning as $item) {
            $list[] = $item;
        }
        if (!$has_error) {
            $error = ["code" => 200, "alert" => "success", "message" => "تحویل بسته بندی $code  برای درخواست " . $product_request_form->code . " بلامانع است. "];
            $list[] = $error;
        }
        return view($this->view_path . "index", compact("dashboard_type","product_request_form", "page", "list", "code"));

    }

}
