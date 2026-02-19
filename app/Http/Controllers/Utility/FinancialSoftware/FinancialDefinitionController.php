<?php

namespace App\Http\Controllers\Utility\FinancialSoftware;

use App\Http\Controllers\Controller;
use App\Models\Utility\Financial\FinancialSoftware;
use Illuminate\Http\Request;

class FinancialDefinitionController extends Controller {
    //
    var $view_path = "utility.financial_software.definition.";
    var $route_path = "utility.financial_software.definition.";

    public function index() {

        $list = FinancialSoftware::where( "id", ">", 0 )->paginate();

        return view( $this->view_path . "index", compact( "list" ) );
    }

    public function edit( FinancialSoftware $financial_software ) {
        $result = $this->checkPermission();
        if ( $result != "" ) {
            return $result;
        }

        return view( $this->view_path . "edit", compact( "financial_software" ) );
    }

    public function update( Request $request, FinancialSoftware $financial_software ) {

        $result = $this->checkPermission();
        if ( $result != "" ) {
            return $result;
        }

        $financial_software->update( $request->all() );

        return redirect()->route( $this->route_path . "index" )->with( [ "success" => "اطلاعات با موفقیت ذخیره گردید." ] );

    }

    public function checkPermission( $button_name = "utility.financial_software.definition.edit_api" ) {


        $post_user = \Auth::user()->posts->first();

        // 615: "sales.dashboard.index";
        if ( $post_user->checkButtonPermission( $button_name ) ) {
            return null;

        } else {
            return back()->withErrors( "صفحه مورد نظر یافت نشد." );

        }


    }
}
