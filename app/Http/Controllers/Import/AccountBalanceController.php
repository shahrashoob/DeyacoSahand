<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Imports\AccountBalanceImport;
use App\Models\Customer\AccountBalance;
use App\Models\Customer\NewAccountBalance;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AccountBalanceController extends Controller
{
    //
    public function index()
    {
        return view("import/account_balance/index");
    }

    public function upload($code)
    {
        NewAccountBalance::where( "id", ">", 0 )->delete();
        $NAB = new AccountBalanceImport();
        $NAB->code = $code;

        Excel::import($NAB, request()->file('file_uploaded'));

        return redirect()->route("import.account_balance.show", $code);
    }

    public function show($code)
    {
        $error_count =0;// NewAccountBalance::where("error", "!=", "")->count();

        $count = NewAccountBalance::count();
        $list = NewAccountBalance::paginate(50);
        return view("import/account_balance/account_balance_list", compact("count", "error_count", "list", "code"));
    }

    public function update($code)
    {
        $list = NewAccountBalance::get();
        foreach ($list as $item) {

            if($item->customer_id!=0) {
                $AB = AccountBalance::firstOrCreate(["customer_id" => $item->customer_id]);

                $debtor = "debtor_" . $code;
                $creditor = "creditor_" . $code;
                $AB->$debtor = $item->debtor;
                $AB->$creditor = $item->creditor;
                $AB->save();
            }
        }

        NewAccountBalance::where("id",">",0)->delete();

        return redirect( )->route("import.account_balance.index")->with(["success"=>"آپلود با موفقیت انجام شده"]);
    }
}
