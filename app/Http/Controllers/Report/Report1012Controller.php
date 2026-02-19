<?php

namespace App\Http\Controllers\Report;

use App\Exports\Report\Report1012_1Export;
use App\Http\Controllers\Controller;
use App\Models\Accounting\Tariff\ProductTariffLog;
use App\Models\Customer\Customer;
use App\Models\Form\FormItem;
use App\Models\Form\FormLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormItemImportantStatus;
use App\Models\Form\Packing\PackingFormItemStatus;
use App\Models\Form\Packing\PackingFormLog;
use App\Models\LineProduct\GoodsKind\GoodsKindPost;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormLog;
use App\Models\Utility\Option;
use App\Models\Utility\Transport\TransportPackingForm;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class Report1012Controller extends Controller {
    // گزارش اکسل گزارش تاریخ های مهم هر بسته بندی
    var $view_path = "report.1012.";
    var $route_path = "report.1012.";
    var $dashboard_path = "report.1012.";
    public static $route_path_static = "report.1012.";

    public function index() {

        $allowed_goods_kind_ids = GoodsKindPost::getAllowedGoodsKindId();
        $goods_kind_option      = Option::get( "goods_kind", 0, 0, $allowed_goods_kind_ids );

        return view( $this->view_path . "index", compact( "goods_kind_option" ) );
    }

    public function submit( Request $request ) {

        $start_date_time = Carbon::parse( $request->start_date . " " . $request->start_time_h . ":" . $request->start_time_m );
        $end_date_time   = Carbon::parse( $request->end_date . " " . $request->end_time_h . ":" . $request->end_time_m );

        $goods_kind_id = $request->goods_kind_id;

        $packing_list_count = PackingFormItemImportantStatus::
        where( "packing_created_at", ">=", $start_date_time )->
        where( "packing_created_at", "<", $end_date_time )->
        where( "goods_kind_id", $goods_kind_id )->
        count();

        if ( $packing_list_count > 800 ) {
            return redirect()->route( $this->dashboard_path . "index" )->withErrors( "با توجه به اینکه تعداد ردیف های فایل خروجی بیش از حد مجاز است، لطفا باز زمانی گزارش را کوچکتر کنید." . $packing_list_count );
        }


        $packing_list = PackingFormItemImportantStatus::
        leftjoin( "product_request_form_important_status", "packing_form_item_important_status.exit_form_id", "product_request_form_important_status.form_id" )->
        leftjoin( "allocation_important_status", "packing_form_item_important_status.allocation_id", "allocation_important_status.allocation_id" )->
        leftjoin( "order_list_important_status", "packing_form_item_important_status.order_id", "order_list_important_status.order_id" )->
        where( "packing_created_at", ">=", $start_date_time )->
        where( "packing_created_at", "<", $end_date_time )->
        where( "goods_kind_id", $goods_kind_id )->
        get();

        $customerList = Customer::get()->keyBy( "id" );

        // کد رنگ
        $color_list = GoodsKindPropertyValue::where( "goods_kind_property_id", 220338 )->pluck( "value", "product_id" )->toArray();

        // عملیات پیمانکار
        $contractor_operation_list = LineProductStation::
        join( "products", "product_id", "products.id" )->
        join( "contractor_operations", "contractor_operation_id", "contractor_operations.id" )->
        whereNotNull( "contractor_operation_id" )->
        where( "goods_kind_id", $goods_kind_id )->
        pluck( "contractor_operations.caption", "product_id" )->
        toArray();
        $export                    = new Report1012_1Export();
        $export->packing_list      = $packing_list;
        $export->customerList      = $customerList;
        $export->color_list        = $color_list;
        $export->contractor_operation_list        = $contractor_operation_list;

        return Excel::download( $export, 'report_1012' . "_" . jdate( Carbon::now()->timestamp )->format( 'Y_m_d' ) . '.xlsx' );

    }


}
