<?php

namespace App\Http\Controllers\Import;

use App\Http\Controllers\Controller;
use App\Models\Customer\NewCustomer;
use App\Models\Order\Permision\OrderPermissionCustomer;
use App\Models\User;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Utility\Option;
use App\Models\Customer\Customer;
use App\Imports\CustomerImport;

class CustomerController extends Controller
{
    //
    public function index()
    {

        $step = 4;


        $model = ["name" => "customer", "route" => "import.customer.upload", "caption" => "فایل لیست مشتریان "];
        return view("import/index", compact("model", "step"));

    }

    public function upload()
    {

        Excel::import(new CustomerImport, request()->file('file_uploaded'));

        return redirect()->route("import.customer.show");

    }

    public function show()
    {
        $list = NewCustomer::orderByDesc("error")->paginate(50);

        $error_count = NewCustomer::where("error", "!=", "")->count();

        return view("import/customer_list", compact("list", "error_count"));
    }

    public function update()
    {

        $list = NewCustomer::orderBy("id")->get();

        foreach ($list as $item) {

            $customer = Customer::where("code", "like", $item->code)->first();
            if (!$customer) {
                $customer = Customer::create($item->toArray());
            } else {
                $customer->update($item->toArray());
            }


            if (!isset($customer->user_id)) {

                $user = User::where("national_code", "like", $customer->national_code)->first();
                if ($user) {

                    $user->firstname = $item->firstname;
                    $user->lastname = $item->lastname;
                    $user->save();

                    $customer->user_id = $user->id;
                    $customer->save();

                } else {
                    $user = new User();
                    $user->firstname = $item->firstname;
                    $user->lastname = $item->lastname;
                    $user->national_code = $item->national_code;
                    $user->email = $customer->national_code;
                    $user->user_type_id = 2;
                    $user->save();
                    $customer->user_id = $user->id;
                    $customer->save();
                }
            } else {
                $user = $customer->user;
                $user->firstname = $item->firstname;
                $user->lastname = $item->lastname;
                $user->national_code = $customer->national_code;
                $user->save();
            }

            $list = [];
            for ($i = 1; $i <= 7; $i++) {
                $c = "order_permission_type_" . $i;
                if ($item->$c == 1) {
                    $list[] = new OrderPermissionCustomer(
                        ["order_permission_type_id" => $i, "customer_id" => $customer->id]);
                }
            }

            $customer->order_permission()->delete();
            $customer->order_permission()->saveMany($list);

        }

        return redirect()->route("dashboard")->with(["success" => "آپلود با موفقیت انجام شده"]);


    }

}
