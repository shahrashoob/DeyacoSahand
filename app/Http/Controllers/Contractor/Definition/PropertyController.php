<?php

namespace App\Http\Controllers\Contractor\Definition;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorProperty;
use App\Models\Contractor\ContractorPropertyValue;
use Illuminate\Http\Request;

class PropertyController extends Controller
{
    var $route_path = "contractor.definition.property.";
    var $view_path = "contractor.definition.property.";

    var $dashboard_path = "contractor.definition.dashboard.index";
    public function index( Contractor $contractor ) {

        $list=ContractorProperty::orderBy("priority_number")->get();
        return view( $this->view_path . "index", compact( "contractor","list" ) );
    }

    public function update( Request $request, Contractor $contractor ) {


        foreach (  ContractorProperty::all() as $item ) {
            $id = "p_" . $item->id;
            if ( isset( $request->$id ) ) {
                $property_value        = ContractorPropertyValue::firstOrCreate( [
                    "contractor_property_id" => $item->id,
                    "contractor_id"     => $contractor->id
                ] );
                $property_value->value = $request->$id;
                $property_value->save();
            }
        }

        return redirect()->route($this->dashboard_path)->with( [ "success" => "اطلاعات با موفقیت ذخیره شد" ] );

    }
}
