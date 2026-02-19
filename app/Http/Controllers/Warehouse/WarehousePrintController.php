<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
//use App\Models\Order\RequestFromWarehouse;
use Illuminate\Http\Request;
use App\Models\Utility\Option;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use App\Models\Order\OrderList;
use App\Models\Order\Order;
use App\Models\Production\Production;
use App\Models\Utility\Pdf;
use Carbon\Carbon;
use App\Models\Warehouse\WarehouseProduct;

class WarehousePrintController extends Controller {
    //
    public function print_rfw( Production $production ) {

        $html = view( "warehouse.print.rfw", compact( "production" ) )->render();

        return Pdf::createAsHtml( $html, "L", $production->serial(), "A5" );

    }

    public function print_rfw_group( Production $production ) {
        $amount = RequestFromWarehouse::where( "master_production_id", $production->id )->
        groupBy( "material_id" )->
        addSelect( DB::raw( "sum(amount) as amount, material_id " ) )->
        pluck( "amount", "material_id" );

        $amount_sent = RequestFromWarehouse::where( "master_production_id", $production->id )->
        groupBy( "material_id" )->
        addSelect( DB::raw( "sum(amount_sent) as amount_sent, material_id " ) )->
        pluck( "amount_sent", "material_id" );

        $amount_remaining = RequestFromWarehouse::where( "master_production_id", $production->id )->
        groupBy( "material_id" )->
        addSelect( DB::raw( "sum(amount_remaining) as amount_remaining, material_id " ) )->
        pluck( "amount_remaining", "material_id" );

        $rfw_list = RequestFromWarehouse::where( "master_production_id", $production->id )->
        groupBy( "material_id" )->get();

        $rfw_production_list = RequestFromWarehouse::where( "master_production_id", $production->id )->
        groupBy( "production_card_id" )->get();

        $html = view( "warehouse.print.rfw_group", compact( "production", "rfw_production_list",
            "rfw_list", "amount", "amount_sent", "amount_remaining" ) )->render();

        return Pdf::createAsHtml( $html, "L", $production->serial(), "A5" );

    }

    public function send_to_nosa() {

        $warehouse_option = Option::get( "warehouse" );

        return view( "warehouse.current_dashboard.send_to_nosa", compact( "warehouse_option" ) );
    }

    public function send_to_nosa_xml( Request $request ) {
        // return $request->all();

        $end_date          = Carbon::create( $request->end_date );
        $end_date          = $end_date->add( 1, 'day' );
        $input_output_type = $request->input_output_type;

        $list = WarehouseProduct::
            join("trans_kinds","trans_kinds.id","trans_kind")->
        where( "warehouse_id", $request->warehouse_id )->
        where( "created_at", ">=", $request->start_date )->
        where( "created_at", "<", $end_date )->
        when( $input_output_type == 1, function ( $query ) {
            return $query->where( "output", 0 );

        } )->
        when( $input_output_type == - 1, function ( $query ) {
            return $query->where( "input", 0 );
        } )->
        when( $request->group_by_ic != "on", function ( $query ) {
            return $query->
            groupBy( "form_id" )->
            groupBy( "product_id" )->
            groupBy( "warehouse_id" )->
            groupBy( "trans_kind" )->
            groupBy( "opp_kind" )->
            groupBy( "ic" )->
            select( "*" )->
            selectRaw( "sum(input) as input, sum(output) as output" );
        } )->

        get();

        // return $list[0]->rfw_form->production->serial();
        $type_description = "default";
        if ( $request->group_by_ic != "on" ) {
            $type_description = "group_by_product";
        }
        $xml = "<?xml version='1.0' encoding='UTF-8' standalone='yes'?>";
        $xml .= view( "warehouse.print.rfw_xml", compact( "list", "input_output_type", "type_description" ) )->render();


        if ( count( $list ) > 0 ) {
            $response = Response::make( $xml, 200 );
            $response->header( 'Content-Type', 'text/xml' );
            $response->header( 'Cache-Control', 'public' );
            $response->header( 'Content-Description', 'File Transfer' );
            $response->header( 'Content-Disposition', 'attachment; filename=' . "material_" . $request->start_date_value . "-" . $request->end_date_value . '.xml' );
            $response->header( 'Content-Transfer-Encoding', 'binary' );

            return $response;
        }

        return back()->withErrors( "هیچ رکوردی وجود ندارد" );


    }

    public function print_request_order( Order $order, $include_header = true ) {
        $order = $order->calculate();
        $html  = view( "warehouse.print.rfw_order", compact( "order", "include_header" ) )->render();

        return Pdf::createAsHtml( $html, "L", $order->code() );

    }

    public function print_request_current_orders() {

        $list = Order::whereIn( "orders.status_id", [ 35020, 35030, 35040, 35050 ] )
                     ->get();

        $html = view( "warehouse.print.current_orders", compact( "list" ) )->render();

        return Pdf::createAsHtml( $html, "L", "current_orders" );

    }

    public function print_palet_sheet() {
        return view( "warehouse.current_dashboard.pallet_sheet" );
    }

    public function palet_sheet_post( Request $request ) {

        $production = Production::where( "serial", $request->serial )->first();
        if ( ! $production ) {
            return back()->withErrors( "شماره سریال نا معتبر می باشد" );
        }

        if ( in_array( $production->waiting_status->id, [ 500010, 500020, 500025, 500030, 500040 ] ) ) {
            return back()->withErrors( "برگ پالت فقط برای کارت هایی که تولید شده قابل دریافت می باشد." );
        }

        $html = view( "warehouse.print.pallet_sheet", compact( "production" ) );

        return Pdf::createAsHtml( $html, "L", "pallet_" . $production->serial(), "A5" );


    }

    public function exit_form( Order $order, Form $form ) {

        $html = view( "warehouse.print.exit_form", compact( "order", "form" ) );

        return Pdf::createAsHtml( $html, "P", "exit_form_" . $form->code(), "A4", '|| ' );
    }


    public function form( Form $form ) {

        if ( $form->trans_kind == 8 ) {
            $production = Production::find( $form->production_card_id );
            if ( ! $production ) {
                return back()->withErrors( "کارت مورد نظر یافت نشد." );
            }
            $material_amounts = $production->RFWs->pluck( "amount", "material_id" );

            $rfw_production_list = RequestFromWarehouse::where( "master_production_id", $production->id )->
            groupBy( "production_card_id" )->get();

            $html = view( "warehouse.print.material_exit_form", compact( "rfw_production_list", "production", "form", "material_amounts" ) );

            return Pdf::createAsHtml( $html, "L", "exit_form_" . $form->code(), "A5", '|| ' );

        } else {
            $warehouseProducts = WarehouseProduct::where( "form_id", $form->id )->get();
            $html              = view( "warehouse.print.entry_form", compact( "form", "warehouseProducts" ) );

            return Pdf::createAsHtml( $html, "L", "entry_form_" . $form->code(), "A5", '|| ' );

        }

    }
}
