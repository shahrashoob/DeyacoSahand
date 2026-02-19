<?php

namespace App\Http\Controllers\Utility\FinancialSoftware;

use App\Http\Controllers\Controller;
use App\Models\Order\TransKind;
use App\Models\Utility\Financial\FinancialSoftware;
use App\Models\Utility\Financial\FinancialSoftwareTransKind;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;


class
FinancialSettingController extends Controller
{
    //
    var $view_path = "utility.financial_software.setting.";
    var $route_path = "utility.financial_software.setting.";

    public function index()
    {
        $result = $this->checkPermission();
        if ($result != "") {
            return $result;
        }
        $list = Warehouse::where("id", ">", 0)->paginate();

        return view($this->view_path . "index", compact("list"));
    }

    public function warehouse(Warehouse $warehouse, FinancialSoftware $financial_software)
    {
        $result = $this->checkPermission();
        if ($result != "") {
            return $result;
        }

        $trans_kind_list = TransKind::get();

        $financial_software_trans_kind = FinancialSoftwareTransKind::where([
            "financial_software_id" => $financial_software->id,
            "warehouse_id" => $warehouse->id
        ])->get()->keyBy("trans_kind_id");

        return view($this->view_path . "warehouse", compact("warehouse", "trans_kind_list", "financial_software_trans_kind"));
    }

    public function submit_warehouse(Request $request, Warehouse $warehouse, FinancialSoftware $financial_software)
    {

        $result = $this->checkPermission();
        if ($result != "") {
            return $result;
        }

        $trans_kind_list = TransKind::get();

        FinancialSoftwareTransKind::where([
            "financial_software_id" => $financial_software->id,
            "warehouse_id" => $warehouse->id
        ])->
        delete();

        foreach ($trans_kind_list as $trans_kind) {
            $has_warehouse_transaction = isset($request->has_warehouse_transaction[$trans_kind->id]);
            $has_accounting_document = isset($request->has_accounting_document[$trans_kind->id]);
            $has_sale_invoice = isset($request->has_sale_invoice[$trans_kind->id]);

            if (
                isset($request->has_accounting_document[$trans_kind->id]) ||
                isset($request->has_warehouse_transaction[$trans_kind->id]) ||
                isset($request->has_sale_invoice[$trans_kind->id]) ||
                isset($request->has_group_by_product[$trans_kind->id])

            ) {

                // شرط های مربوط به ثبت تراکنش انبار
                if ($has_warehouse_transaction) {
                    if (!isset($request->inv_kind_code[$trans_kind->id])) {
                        return back()->withErrors("لطفا کد نوع برگه انبار را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                    if (!isset($request->dept_code[$trans_kind->id])) {
                        return back()->withErrors("لطفا کد بخش را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                    if (!isset($request->inv_series[$trans_kind->id])) {
                        return back()->withErrors("لطفا سری را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                }

                // شرایط فاکتور فروش
                if ($has_sale_invoice) {
                    if (!isset($request->delivery_cond_code[$trans_kind->id])) {
                        return back()->withErrors("لطفا کد شرایط تحویل را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                    if (!isset($request->payment_method_code[$trans_kind->id])) {
                        return back()->withErrors("لطفا کد نحوه پرداخت را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                    if (!isset($request->sales_center_code[$trans_kind->id])) {
                        return back()->withErrors("لطفا کد مرکز فروش را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                    if (!isset($request->deb_side[$trans_kind->id])) {
                        return back()->withErrors("لطفا طرف بدهکار را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }
                    if (!isset($request->calc_state[$trans_kind->id])) {
                        return back()->withErrors("لطفا وضعیت محاسبه کسورو اضافات و پورسانت ها را برای رخداد " . $trans_kind->caption . " انتخاب نمایید.");
                    }

                }

                FinancialSoftwareTransKind::create([
                    "financial_software_id" => $financial_software->id,
                    "warehouse_id" => $warehouse->id,
                    "trans_kind_id" => $trans_kind->id,
                    "has_accounting_document" => $has_accounting_document,
                    "has_warehouse_transaction" => $has_warehouse_transaction,
                    "has_sale_invoice" => $has_sale_invoice,
                    "has_group_by_product" => isset($request->has_group_by_product[$trans_kind->id]),
                    "inv_kind_code" => $request->inv_kind_code[$trans_kind->id],
                    "dept_code" => $request->dept_code[$trans_kind->id],
                    "inv_series" => $request->inv_series[$trans_kind->id],
                    "delivery_cond_code"=>$request->delivery_cond_code[$trans_kind->id],
                    "payment_method_code"=>$request->payment_method_code[$trans_kind->id],
                    "sales_center_code"=>$request->sales_center_code[$trans_kind->id],
                    "deb_side"=>$request->deb_side[$trans_kind->id],
                    "calc_state"=>$request->calc_state[$trans_kind->id]
                ]);
            }
        }

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات با موفقیت ذخیره گردید."]);

    }

    public function checkPermission($button_name = "utility.financial_software.setting.index")
    {


        $post_user = \Auth::user()->posts->first();

        // 615: "sales.dashboard.index";
        if ($post_user->checkButtonPermission($button_name)) {
            return null;

        } else {
            return back()->withErrors("صفحه مورد نظر یافت نشد.");

        }


    }
}
