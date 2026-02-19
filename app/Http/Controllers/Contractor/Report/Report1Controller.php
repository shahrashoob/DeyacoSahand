<?php

namespace App\Http\Controllers\Contractor\Report;

use App\Exports\Contractor\Report\Report1Export;
use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\ContractorPost;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\Order\Order;
use App\Models\Utility\DateTime;
use App\Models\Utility\Option;
use App\Models\Utility\Pdf;
use App\Models\Utility\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class Report1Controller extends Controller {
    //
    var $view_path = "contractor.report.report1.";
    var $route_path = "contractor.report.report1.";

    public function index() {
        $result = $this->checkPermission();
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        $contractor_ids = ContractorPost::whereIn( "post_id", $result["post_ids"] )->pluck( "contractor_id" )->toArray();

        $goods_kind_option = Option::get( "goods_kind" );
        $order_by_Option   = Option::OrderBy( "contractor_allocation", 0 );

        $waiting_status_option = Option::get( "status", 0, 5311 );

        $property_option = Option::get( "get_property_by_goods_kind", 0, 0 );

        return view( $this->view_path . "index", compact( "contractor_ids",
            "goods_kind_option", "order_by_Option", "waiting_status_option", "property_option" ) );
    }

    public function submit( Request $request ) {
//          return $request->all();
        $result = $this->checkPermission();
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }
        $goods_kind_property_id = $request->goods_kind_property_id;
        $search                 = $request->search;
        $order_by               = $request->order_by;
        $order_search           = $request->order_search;
        $waiting_status_id      = $request->waiting_status_id;
        $start_date_time        = DateTime::getDateTimeFromRequest( $request, "start_date" );
        $end_date_time          = DateTime::getDateTimeFromRequest( $request, "end_date" )->addSecond(24*60*60-1);

        // جستجوی بر اساس مشخصه کالا
        $product_ids = [];

        if ( $goods_kind_property_id ) {
            $product_ids   = GoodsKindPropertyValue::where( [
                "goods_kind_property_id" => $goods_kind_property_id,
                "value"                  => $search
            ] )->pluck( "product_id" )->toArray();
            $product_ids[] = 0;

        }

// جستجوی سفارش
        $order_ids = [];

        if ( $order_search != "" ) {
            $order_ids = Order::where( "code", "like", "%" . $order_search . "%" )->
            orWhere( "series", "like", "%" . $order_search . "%" )->pluck( "id" );
        }

        $contractor_ids = ContractorPost::whereIn( "post_id", $result["post_ids"] )->pluck( "contractor_id" )->toArray();

        $machine_allocation_ids = ContractorAllocation::
        join( "production_cards", "production_id", "production_cards.id" )->
        join( "products", "machine_allocation.product_id", "products.id" )->
        whereIn( "machine_allocation.contractor_id", $contractor_ids )->
        where( "supply_type_id", 3 )->
        when( ! $goods_kind_property_id && $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                $query->where( "serial", "like", "%" . $search . "%" );
                $query->orWhere( "products.code", "like", "%" . $search . "%" );
                $query->orWhere( "products.caption", "like", "%" . $search . "%" );
            } );

        } )->
//        when( $order_by, function ( $query ) use ( $order_by ) {
//            $order_by = Str::of( $order_by )->explode( "__" );
//
//            return $query->orderBy( $order_by[0], $order_by[1] );
//
//        } )->
        when( $product_ids != [], function ( $query ) use ( $product_ids ) {
            return $query->whereIn( "production_cards.product_id", $product_ids );
        } )->
        when( $order_ids != [], function ( $query ) use ( $order_ids ) {
            return $query->whereIn( "production_cards.order_id", $order_ids );

        } )->
        pluck( "machine_allocation.id" )->
        toArray();

        if ( $request->report_type == "excel" ) {

            $list         = MachineAllocationPackingForm::whereIn( "machine_allocation_id", $machine_allocation_ids )->
            where( "created_at", ">=", $start_date_time )->
            where( "created_at", "<", $end_date_time )->
            orderBy( "created_at" )->
            get();
            $export       = new Report1Export();
            $export->list = $list;

            return Excel::download( $export, 'contractor_report_1_' . jdate( Carbon::now()->timestamp )->format( 'Y_m_d' ) . '.xlsx' );

        } else {

            $list = PackingFormItem::
            join( "machine_allocation_packing_form", "machine_allocation_packing_form.packing_form_id", "packing_form_item.packing_form_id" )->
            whereIn( "machine_allocation_id", $machine_allocation_ids )->
            where( "machine_allocation_packing_form.created_at", ">=", $start_date_time )->
            where( "machine_allocation_packing_form.created_at", "<=", $end_date_time )->
            groupBy( "machine_allocation_id" )->
            groupBy( "product_id" )->
            addSelect( DB::raw("sum(final_amount) as amount, round(sum(sub_amount),2) as sub_amount, count(distinct packing_form_item.packing_form_id) as packing_form_count, product_id,production_form_item_id") )->
            get();



            $header_text = Setting::getStringValue( "transport_loading_header_text" );
            $start_date=jdate( Carbon::parse( $start_date_time )->timestamp )->format( 'H:i Y/m/d ' );
            $end_date=jdate( Carbon::parse( $end_date_time )->timestamp )->format( 'H:i Y/m/d ' );


            $view_path = "contractor.report.print_report2.";
            $html      = [];
            $html[0]   = view( $view_path . "_head" )->render();
            $html[0]   .= view( $view_path . "_print_transport",
                    compact( "list","header_text","start_date","end_date","search","order_search" ) )->render() . $html[0];
            $html[0]   .= view( $view_path . "_footer" )->render();
            Pdf::createAsHtml( $html,
                "P",
                "Contractor_report2",
                "A4",
                " "
            );
        }


    }

    private function checkPermission( $contractor_allocation = null ) {

        $post_ids = Auth::user()->posts->pluck( "post_id" )->toArray();

        if ( $contractor_allocation ) {
            $exist_permission = ContractorPost::
            where( [ "contractor_id" => $contractor_allocation->contractor_id ] )->
            whereIn( "post_id", $post_ids )->
            exists();
            if ( ! $exist_permission ) {
                return [
                    "result"  => false,
                    "message" => "دسترسی  مشاهده اطلاعات پیمانکار برای شما تعریف نشده است.",
                ];
            }
        }

        return [
            "result"   => true,
            "post_ids" => $post_ids
        ];
    }
}
