<?php

namespace App\Http\Controllers\Accounting\Setting;

use App\Http\Controllers\Controller;
use App\Models\HR\Shift\ShiftDeliveryModule;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use function Aws\recursive_dir_iterator;

class DashboardController extends Controller {
    //
    var $route_path = "accounting.setting.dashboard.";
    var $view_path = "accounting.setting.dashboard.";

    public function index() {

        $list   = ShiftDeliveryModule::paginate();
        $values = Setting::getValues();

        return view( $this->view_path . "index", compact( "list", "values" ) );
    }

    public function edit_module( ShiftDeliveryModule $shift_delivery_module ) {

        return view( $this->view_path . "edit_module", compact( "shift_delivery_module" ) );
    }

    public function update_module( Request $request, ShiftDeliveryModule $shift_delivery_module ) {

        $shift_delivery_module->update( $request->all() );

        return redirect()->route( $this->route_path . "edit_module",$shift_delivery_module )->with( [ "success" => "اطلاعات با موفقیت ذخیره گردید." ] );
    }
}
