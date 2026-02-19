<?php

namespace App\Http\Controllers\HR\Employment\Register\Personal;

use App\Events\HR\EmploymentLogEvent;
use App\Http\Controllers\Controller;
use App\Models\HR\Employment\Employment;
use App\Models\HR\User\UserBankAccount;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
/*
این کنترلر برای  ثبت اطلاعات بانکی می باشد.
 * */
class BankInformationController extends Controller
{
    protected $view_path = "hr.employment.register.personal.bank_information.";
    protected $route_path = "hr.employment.register.personal.bank_information.";
    protected $next_route = "hr.employment.register.personal.confirm_drafting_contract.index";

    public function index($key)
    {
        $employment = Employment::where("key", $key)->
        whereIn("status_id", [4640115,4640121,4640118])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        $bank_names_option = Option::get("bank_names");
        $allow_delete=true;
        return view($this->view_path . "index", compact('employment','bank_names_option', 'allow_delete'));

    }

    public function submit($key, Request $request)
    {

        $employment = Employment::where("key", $key)->
        whereIn("status_id", [4640115,4640121,4640118])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }

        if ($employment->worker->user_bank_accounts->count() >= 1) {
            return back()->withErrors(" ثبت اطلاعات بانکی بیش از  1 مورد امکان پذیر نمی باشد. ");
        }
        UserBankAccount::create([
            'user_id'=>$employment->user_id,
            'account_number'=> $request->account_number,
            'shaba_number'=> $request->shaba_number,
            'card_number'=> $request->card_number ??"",
            'bank_id'=> $request->bank_id,
            'bank_branch'=> $request->bank_branch,
        ]);
        $employment->status_id = 4640115;//در انتظار تایید قرارداد
        $employment->save();

        event(new EmploymentLogEvent($employment, 4640025, null, null, $employment->user_id));
        return redirect()->route($this->next_route, $employment->key)->with(["success" => "اطلاعات بانکی با موفقیت ثبت گردید.<br/>لطفا قرارداد زیر را با دقت مطالعه نموده و آن را تایید نمایید."]);


    }
    public function destroy($key, UserBankAccount $user_bank_account)
    {
        $employment = Employment::where("key", $key)->
        whereIn("status_id", [4640115,4640121,4640118])->
        first();
        if (!$employment) {
            return redirect()->route("login")->withErrors("اطلاعات درخواست همکاری نامعتبر است.");
        }
        if ($employment->user_id != $user_bank_account->user_id) {
            return back()->withErrors("اطلاعات بانکی جهت حذف نامعتبر است.");
        }
        $user_bank_account->delete();

        return back()->with(["success" => "یک اطلاعات بانکی با موفقیت حذف گردید."]);

    }


}
