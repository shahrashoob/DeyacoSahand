<?php

namespace App\Http\Controllers\HR\Employment\Admin\Personal;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\HR\Employment\Employment;
use App\Models\Utility\Option;
use Illuminate\Http\Request;


class CostCenterController extends Controller {
    //


    protected $view_path = "hr.employment.admin.personal.cost_center.";
    protected $route_path = "hr.employment.admin.personal.cost_center.";



    public function create(Employment $employment) {
        $status_option = Option::get( "status", 0, 1100 );
        $cost_center   = new CostCenter();
        return view( $this->view_path . "create", compact( "cost_center", "status_option","employment" ) );
    }

    public function store( Request $request ,Employment $employment) {

        $cost_center=\App\Http\Controllers\Accounting\Definition\CostCenterController::CreateCostCenter($request);
        if (!$cost_center['result']) {
            return back()->withErrors($cost_center['error']);
        }
        return redirect()->route( "hr.employment.admin.personal.financial_information.index",$employment)->with( [ "success" => "مرکز هزینه با موفقیت اضافه شد" ] );

    }


}
