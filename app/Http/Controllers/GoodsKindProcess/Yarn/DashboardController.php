<?php

namespace App\Http\Controllers\GoodsKindProcess\Yarn;

use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use Illuminate\Http\Request;

class DashboardController extends Controller {
    //
    var $view_path = "goods_kind_process.yarn.dashboard.";
    var $route_path = "yarn.dashboard.";

    public function index() {
        $list = Form::where( [ "form_type_id" => 302 ] )->where( "status_id", "!=", 500000100 )->paginate();

        return view( $this->view_path . "index", compact( "list" ) );
    }

    public function show_form(Form $form ) {
        if ( $form->form_type_id != 302 ) {
            return back()->withErrors( "نوع فرم به درستی انتخاب نشده است" );
        }
        $form_item = $form->item()->first();
        $product   = $form_item->product;
        return view($this->view_path."show_form",compact("form","product","form_item"));
    }
    public function confirm_warehouse(Form $form){
        if ( $form->form_type_id != 302 || $form->status_id !=500000400 ) {
            return back()->withErrors( "نوع فرم | وضعیت فرم  به درستی انتخاب نشده است" );
        }

        $form->status_id = 500000410;
        $form->save();
        event( new FormLogEvent( $form ) );
        return redirect()->route( $this->route_path."index" )->with( [ "success" => "فرم با موفقیت ثبت گردید" ] );

    }
}

