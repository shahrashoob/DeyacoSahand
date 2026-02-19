<?php

namespace App\Http\Controllers\Contractor\Definition;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorOperation;
use App\Models\LineProduct\StationOperation;
use Illuminate\Http\Request;

class OperationController extends Controller {
    var $route_path = "contractor.definition.operation.";
    var $view_path = "contractor.definition.operation.";

    var $dashboard_path = "contractor.definition.dashboard.index";

    public function index( Contractor $contractor ) {
        return view( $this->view_path . "index", compact( "contractor" ) );
    }

    public function create( Contractor $contractor ) {
        return view( $this->route_path."create", compact( "contractor" ) );
    }

    public function store( Request $request, Contractor $contractor ) {

        $result = ContractorOperation::
        where( "contractor_id", $contractor->id )->
        where( "caption", $request->caption )->
        exists();
        if ( $result ) {
            return back()->withErrors( "عنوان عملیات تکراری است." );
        }

        ContractorOperation::create( [
            "contractor_id" => $contractor->id,
            "caption"       => $request->caption
        ] );

        return redirect()->
        route( $this->route_path."index",$contractor  )->
        with( [ "success" => "یک عملیات جدید اضافه گردید." ] );
    }

    public function edit( Contractor $contractor, ContractorOperation $contractor_operation ) {

        return view( $this->view_path."edit", compact( "contractor_operation", "contractor" ) );
    }

    public function update( Request $request, ContractorOperation $contractor_operation ) {

        $contractor_operation->update( $request->all() );

        return redirect()->
        route( $this->route_path."index",$contractor_operation->contractor  )->
        with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }

}
