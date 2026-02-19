<?php

namespace App\Http\Controllers\Customer\Definition;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Utility\Option;
use App\Models\Worker;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BasicInformationController extends Controller {
    //
    public $route_path = "customer_group.definition.basic_information.";
    public $view_path = "customer.definition.basic_information.";

    public function index( Customer $customer ) {

        $worker               = Worker::find( Auth::id() );
        $customer_type_option = Option::get( "customer_type", $customer->customer_type_id ?? 1 );
        $gender_option        = Option::get( "gender", $customer->gender_id ?? 0 );

        $province_option = Option::get( "province", $customer->province_id ?? 0 );

        $address        = $customer->getDefaultAddress();

        $country_option = Option::get( "country", $address->country->id ?? 112 );

        return view( $this->view_path . "index", compact( "customer", "worker", "customer_type_option", "gender_option", "address", "country_option", "province_option" ) );

    }

    public function submit( Request $request, Customer $customer ) {


// یکی کردن تاریخ تولد و جنسیت
        if ( $request->customer_type_id == 1 ) {
            $request["gender_id"]     = $request->gender_id1;
            $request["birth_date"]    = $request->birth_date1;
            $request["caption"]       = $request->caption1;
            $request["firstname"]     = $request->firstname1;
            $request["lastname"]      = $request->lastname1;
            $request["gender_id"]     = $request->gender_id1;
            $request["national_code"] = $request->national_code1;
        } else {

            $request["gender_id"]       = $request->gender_id2;
            $request["birth_date"]      = $request->birth_date2;
            $request["caption"]         = $request->caption2;
            $request["firstname"]       = $request->firstname2;
            $request["lastname"]        = $request->lastname2;
            $request["gender_id"]       = $request->gender_id2;
            $request["national_code"]   = $request->national_code2;
            $request["register_code"]   = $request->register_code2;
            $request["economic_number"] = $request->economic_number2;
        }

        $national_code_count = User::where( "national_code", "like", $request->national_code ?? "" )->
        where( "id", "!=", $customer->user_id ?? 0 )->exists();
        if ( $national_code_count ) {
            return back()->withErrors( "کد ملی مشابه در سیستم وجود دارد" );
        }



         $customer->update( $request->all() );
        $customer->user->update( $request->all() );
        // ذخیره آدرس
        $customer->UpdateAddress( $request, 1 );


       return redirect()->route( "dashboard" )->with( [ "success" => "اطلاعات  با موفقیت بروز رسانی شد." ] );


    }
}
