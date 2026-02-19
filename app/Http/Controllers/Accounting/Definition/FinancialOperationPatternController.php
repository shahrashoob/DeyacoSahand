<?php

namespace App\Http\Controllers\Accounting\Definition;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Account;
use App\Models\Accounting\FinancialOperation\FinancialOperationPattern;
use App\Models\Accounting\FinancialOperation\FinancialOperationPatternItem;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Calculation\Statistical\Distributions\F;

class FinancialOperationPatternController extends Controller
{
    private $view_path = "accounting.definition.financial_operation_pattern.";
    private $route_path = "accounting.definition.financial_operation_pattern.";

    //
    public function index()
    {
        $list = FinancialOperationPattern::all();
        return view($this->view_path . "index", compact("list"));
    }

    public function create()
    {
        $financial_operation_pattern_type_option = Option::get('financial_operation_pattern_type');

        return view($this->view_path . "create", compact("financial_operation_pattern_type_option"));
    }

    public function store(Request $request)
    {

        if ($request->caption == "" || FinancialOperationPattern::ExistsCaption($request->caption, null)) {
            return back()->withErrors("عنوان الگو تکراری است");
        }
        $request["register_detailed_code_for_customer"] = $request->register_detailed_code_for_customer ? 1 : 0;

        FinancialOperationPattern::create($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "یک الگوی عملیات با موفقیت اضافه شد"]);

    }

    public function edit(FinancialOperationPattern $financial_operation_pattern)
    {
        $financial_operation_pattern_type_option = Option::get('financial_operation_pattern_type', $financial_operation_pattern->id);

        return view($this->view_path . "edit", compact("financial_operation_pattern", "financial_operation_pattern_type_option"));
    }

    public function update(Request $request, FinancialOperationPattern $financialOperationPattern)
    {

        if ($request->caption == "" || FinancialOperationPattern::ExistsCaption($request->caption, $financialOperationPattern->id)) {
            return back()->withErrors("عنوان الگو تکراری است");
        }

        $request["register_detailed_code_for_customer"] = $request->register_detailed_code_for_customer ? 1 : 0;
        $financialOperationPattern->update($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "اطلاعات عملیات با ذخیره شد"]);

    }

    public function add_item(FinancialOperationPattern $financial_operation_pattern)
    {

        $account_option = Option::get("account");
        $financial_operation_pattern_item_types_option = Option::get("financial_operation_pattern_item_types");
        return view($this->view_path . "add_item", compact("financial_operation_pattern", "account_option", "financial_operation_pattern_item_types_option"));
    }

    public function submit_add_item(Request $request, FinancialOperationPattern $financialOperationPattern)
    {
        if (!$request->account_id || !Account::find($request->account_id)) {
            return back()->withErrors("کد حساب نامعتبر است");
        }
        if ($request->financial_operation_pattern_item_type_id<=0) {
            return back()->withErrors("لطفا نوع ارتباط حساب با الگوی مالی را انتخاب نمایید.");
        }
        $account = $financialOperationPattern->items()->where("account_id", $request->account_id)->first();

        if ($account || !$request->account_id) {
            return back()->withErrors("کد حساب تکراری است");
        }
        if (in_array($request->financial_operation_pattern_item_type_id, [1, 2, 3])) {
            // حداکثر یک حساب ارزش افزوده و تخفیف می تواند به حساب اضافه کنند
            $count = FinancialOperationPatternItem::where([
                "financial_operation_pattern_id" => $financialOperationPattern->id,
                "financial_operation_pattern_item_type_id" => $request->financial_operation_pattern_item_type_id,
            ])->count();
            if ($count > 0) {
                return back()->withErrors("حداکثر یک حساب مرتبط با تخفیف (ارزش افزوده) می توان اضافه کرد، لطفا حساب قبلی تخفیف (ارزش افزوده) را حذف کنید و مجدد تلاش کنید.");
            }
        }

        FinancialOperationPatternItem::create([
            "financial_operation_pattern_id" => $financialOperationPattern->id,
            "financial_operation_pattern_item_type_id" => $request->financial_operation_pattern_item_type_id,
            "account_id" => $request->account_id
        ]);
        return redirect()->route($this->route_path . "index")->with(["success" => "یک حساب با موفقیت به الگوی مالی اضافه گردید."]);

    }

    public function remove_item(FinancialOperationPattern $financialOperationPattern, FinancialOperationPatternItem $financialOperationPatternItem)
    {
        if ($financialOperationPatternItem->financial_operation_pattern_id != $financialOperationPattern->id) {
            return back()->withErrors("اطلاعات به درستی وارد نشده است، لطفا مجددا تلاش کنید.");
        }
        $financialOperationPatternItem->delete();
        return back()->with(["success" => "یک حساب با موفقیت حذف گردید."]);
    }
}
