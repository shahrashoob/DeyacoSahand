<?php

namespace App\Http\Controllers\Accounting\Definition;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Account;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use function back;
use function redirect;
use function session;
use function view;

class AccountController extends Controller
{
    //
    private $view_path = "accounting.definition.account.";
    private $route_path = "accounting.definition.account.";

    public function index(Request $request)
    {
//        if ( $request->isMethod( 'post' ) ) {
//            $search   = $request->search;
//            $order_by = $request->order_by;
//        } else {
//            $search   = session( "search_cost_center" );
//            $order_by = session( "order_by_cost_center" );
//        }
//        session( [ "search_cost_center" => $search, "order_by_cost_center" => $order_by ] );


        $list = Account::whereNull("parent_id")->paginate(50);

        return view($this->view_path . "index", compact("list"));
    }

    public function account_list(Account $account)
    {

        $list = Account::where("parent_id", $account->id)->paginate(50);

        return view($this->view_path . "account_list", compact("list", "account"));
    }

    public function create()
    {


        return view($this->view_path . "create");
    }

    public function store(Request $request)
    {
        if ($request->code == "" || Account::ExistsCode($request->code, null)) {
            return back()->withErrors("کد حساب تکراری است");
        }
        if ($request->caption == "" || Account::ExistsCaption($request->caption, null)) {
            return back()->withErrors("عنوان حساب تکراری است");
        }

        $request["has_separator_in_full_code"] = $request->has_separator_in_full_code ? 1 : 0;
        Account::create($request->all());

        return redirect()->route($this->route_path . "index")->with(["success" => "یک حساب با موفقیت اضافه شد"]);

    }

    public function create_sub_account(Account $account)
    {
        $result = Account::AllowCreateSubAccount($account);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        return view($this->view_path . "create_sub_account", compact("account"));
    }

    public function store_sub_account(Request $request, Account $account)
    {
        if ($request->code == "" || Account::ExistsCode($request->code, $account->id, $account->id)) {
            return back()->withErrors("کد حساب تکراری است");
        }
        if ($request->caption == "" || Account::ExistsCaption($request->caption, $account->id, $account->id)) {
            return back()->withErrors("عنوان حساب تکراری است");
        }
        $result = Account::AllowCreateSubAccount($account);
        if (!$result["result"]) {
            return back()->withErrors($result["error"]);
        }

        $request["parent_id"] = $account->id;
        $request["full_code"] = "";
        $request["has_separator_in_full_code"] = $request->has_separator_in_full_code ? 1 : 0;
        Account::create($request->all());
        Account::UpdateFullCodeFroAllSubAccount($account);
        return redirect()->route($this->route_path . "account_list", $account)->with(["success" => "یک حساب با موفقیت اضافه شد"]);

    }

    public function edit(Account $account)
    {

        return view($this->view_path . "edit", compact("account"));

    }

    public function update(Request $request, Account $account)
    {
        if ($request->code == "" || Account::ExistsCode($request->code, $account->parent_id ?? null, $account->id)) {
            return back()->withErrors("کد تکراری است");
        }
        if ($request->caption == "" || Account::ExistsCaption($request->caption, $account->parent_id ?? null, $account->id)) {
            return back()->withErrors("عنوان حساب تکراری است");
        }

        $request["has_separator_in_full_code"] = $request->has_separator_in_full_code ? 1 : 0;
        $account->update($request->all());
        Account::UpdateFullCodeFroAllSubAccount($account);
        return redirect()->route($this->route_path . "account_list", $account)->with(["success" => "اطلاعات با موفقیت ذخیره شد"]);

    }
//
//    public function destroy( CostCenter $cost_center ) {
//        if (
//            MachineType::where( "ic", $cost_center->id )->exists()
//        ) {
//            return back()->withErrors( "به دلیل استفاده شدن در رکوردهای دیگر، امکان حذف وجود ندارد" );
//        }
//        $cost_center->delete();
//
//        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "یک آیتم با موفقیت حذف گردید" ] );
//
//    }
}
