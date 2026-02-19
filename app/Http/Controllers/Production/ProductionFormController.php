<?php

namespace App\Http\Controllers\Production;

use App\Http\Controllers\Controller;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ProductionFormController extends Controller
{

    var $view_path = "production.production_form.";
    var $route_path = "production.production_form.";
    public function index( Request $request ) {

        if ( $request->isMethod( 'post' ) ) {
            $search    = $request->search;
            $order_by  = $request->order_by;
            $status_id = $request->status_id;
        } else {
            $search    = session( "search_production_form" );
            $status_id = session( "production_form_status_id" );
            $order_by  = session( "order_by_production_form" ) ?? "created_at__desc";
        }
        session( [
            "search_production_form"    => $search,
            "production_form_status_id" => $status_id,
            "order_by_production_form"  => $order_by,
        ] );
        $allowed_status_ids = PostStatus::getAllowedStatus();
        $allowed_status_ids_all=$allowed_status_ids;
        // search
        if ( $status_id != 0 ) {
            $allowed_status_ids   = [];
            $allowed_status_ids[] = $status_id;
        }
        if ( $status_id != 0 && ! in_array( $status_id, $allowed_status_ids ) ) {
            $allowed_status_ids = [];
        }
        $list = ProductionForm::
        join( "machines", "machines.id", "machine_id" )->
        leftjoin( "carriers", "carrier_id", "carriers.id" )->
        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "production_forms.code", "like", "%" . $search . "%" )->
                orWhere( "machines.caption", "like", "%" . $search . "%" )->
                orWhere( "machines.code", "like", "%" . $search . "%" )->
                orWhere( "carriers.code", "like", "%" . $search . "%" );
            } );

        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            if($order_by== "created_at__asc" || $order_by== "created_at__desc" ){
                $order_by="production_forms.".$order_by;
            }
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );
        } )->
        when( $allowed_status_ids != [], function ( $query ) use ( $allowed_status_ids ) {
            return $query->where( function ( $query ) use ( $allowed_status_ids ) {
                $query->whereIn( "production_forms.status_id", $allowed_status_ids );
            } );
        } )->
        select( "production_forms.id", "machine_id", "carrier_id", "production_forms.code", "production_forms.status_id", "production_forms.created_at" )->
        paginate( 50 );

        $order_by_Option = Option::OrderBy( "production_form", $order_by );

        $status_option = Option::get( "production_form_status", $status_id, [7002,7202,7302],$allowed_status_ids_all );
        $route_path=$this->route_path;
        return \view( $this->view_path . "index", compact( "list", "order_by_Option", "search", "status_option" ,"route_path") );
    }

    public function view( ProductionForm $production_form ) {

        $production_form_item=$production_form->items()->first();
        if(!$production_form_item){
            return back()->withErrors("فرم انتخاب شده، هیچ رکوردی ندارد.");
        }
        if (!in_array( $production_form_item->product->goods_kind->caption_en , [ "Fabric_Raw","Warps","Fabric"]) ) {
            return redirect()->back()->withErrors( "فرایند کارت تولید مربوط به رسته کالا یافت نشد.  " );
        }

        return redirect()->route(
            Str::lower( $production_form_item->product->goods_kind->caption_en ) . ".production_form.view",
            $production_form
        );

    }

}
