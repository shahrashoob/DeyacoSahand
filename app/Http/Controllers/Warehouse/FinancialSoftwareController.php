<?php

namespace App\Http\Controllers\Warehouse;

use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Post\Post;
use App\Models\Utility\Financial\FinancialSoftwareTransferForm;
use App\Models\Utility\Financial\FinancialSoftwareTransKind;
use App\Models\Utility\Financial\FinancialSoftwareTransKindLog;
use App\Models\Utility\Financial\FinancialSoftwareTransKindType;
use App\Models\Warehouse\Warehouse;
use Illuminate\Http\Request;
use App\Models\Utility\Option;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;
use App\Models\Warehouse\WarehouseProduct;

class FinancialSoftwareController extends Controller {
    //
    var $view_path = "warehouse.financial_software.";
    var $route_path = "wh.financial_software.";

    public function index() {

        $warehouse_option = Option::get( "warehouse" );

        return view( $this->view_path . "index", compact( "warehouse_option" ) );
    }

    public function submit_nosa_xml( Request $request ) {
        //return $request->all();

        $end_date          = Carbon::create( $request->end_date );
        $end_date          = $end_date->add( 1, 'day' );
        $input_output_type = $request->input_output_type;
        $all_transaction   = isset( $request->all_transaction ) ? 1 : 0;

        $list_trans_kind = WarehouseProduct::
        where( "warehouse_id", $request->warehouse_id )->
        where( "created_at", ">=", $request->start_date )->
        where( "created_at", "<", $end_date )->
        groupBy( "trans_kind" )->get();

        $financial_software_trans_kind = FinancialSoftwareTransKind::where( [
            "financial_software_id" => 1,
            "warehouse_id"          => $request->warehouse_id
        ] )->get()->keyBy( "trans_kind_id" );

//        if ( count( $financial_software_trans_kind ) == 0 ) {
//            $warehouse = Warehouse::find( $request->warehouse_id );
//
//            return back()->withErrors( "تنظیمات ثبت تراکنش برای " . $warehouse->caption . " انجام نشده است." );
//        }

        $xml_nodes = "";

        foreach ( $list_trans_kind as $trans_kind_row ) {

            if ( isset( $financial_software_trans_kind[ $trans_kind_row->trans_kind ] ) ) {
                $has_group_by_product = $financial_software_trans_kind[ $trans_kind_row->trans_kind ]->has_group_by_product;
            } else {
                $has_group_by_product = 1;
            }


            // اگر تیک همه تراکنش ها T است، فقط آنهایی که در API تراکنش انبار
            if (
                $all_transaction
            ) {

                $xml_nodes .= $this->getXMLItem( $request->warehouse_id, $trans_kind_row->trans_kind, $request->start_date, $end_date, $request->input_output_type, $has_group_by_product );

            } else {
                // اگر تیک همه تراکنش ها F است، فقط تراکنش هایی که در تنظیمات مالی تیک تراکنش انبار ندارند، xml آنها ثبت می شود.
                $has_warehouse_transaction = isset( $financial_software_trans_kind[ $trans_kind_row->trans_kind ] ) &&
                                             $financial_software_trans_kind[ $trans_kind_row->trans_kind ]->has_warehouse_transaction ? 1 : 0;

                if ( ! $has_warehouse_transaction ) {

                    $xml_nodes .= $this->getXMLItem( $request->warehouse_id, $trans_kind_row->trans_kind, $request->start_date, $end_date, $request->input_output_type, $has_group_by_product );

                }

            }

        }


        if ( $xml_nodes != "" ) {
            $xml      = "<?xml version = '1.0' encoding = 'UTF-8' standalone = 'yes'?>\n <_XPXML>" . $xml_nodes . "</_XPXML>";
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

    public function getXMLItem( $warehouse_id, $trans_kind_id, $start_date, $end_date, $input_output_type, $has_group_by_product ) {
        // return $trans_kind_id;
        // با توجه به تنظیمات هر نوع رخداد لیست را آماده می کنیم.
        $list = WarehouseProduct::
        join( "trans_kinds", "trans_kinds.id", "trans_kind" )->
        where( "trans_kinds.id", $trans_kind_id )->
        where( "trans_kinds.nosa_code", "!=", 0 )->
        where( "warehouse_id", $warehouse_id )->
        where( "created_at", ">=", $start_date )->
        where( "created_at", "<", $end_date )->
        when( $input_output_type == 1, function ( $query ) {
            return $query->where( "output", 0 );

        } )->
        when( $input_output_type == - 1, function ( $query ) {
            return $query->where( "input", 0 );
        } )->
        when( $has_group_by_product == 1, function ( $query ) {
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


        $type_description = $has_group_by_product == 1 ? "group_by_product" : "default";

        $xml_nodes = view( "warehouse.financial_software.nosa_warehouse_transaction_node_xml", compact( "list", "input_output_type", "type_description" ) )->render();


        return $xml_nodes;
    }

    public function form_list( Request $request ) {

        if ( $request->isMethod( 'post' ) ) {
            $search              = $request->search;
            $status_id           = $request->status_id;
            $financial_status_id = $request->financial_status_id;
            $warehouse_id        = $request->warehouse_id;
            $order_by            = $request->order_by;
        } else {
            $search              = session( "search_warehouse_input" );
            $status_id           = session( "warehouse_input_status_id" );
            $financial_status_id = session( "warehouse_input_financial_status_id" );
            $warehouse_id        = session( "warehouse_input_warehouse_id" );
            $order_by            = session( "warehouse_input_order_by" ) ?? "forms.created_at__desc";

        }
        session( [
            "search_warehouse_input"              => $search,
            "warehouse_input_status_id"           => $status_id,
            "warehouse_input_financial_status_id" => $financial_status_id,
            "warehouse_input_warehouse_id"        => $warehouse_id,
            "warehouse_input_order_by"            => $order_by,
        ] );

        $allowed_status_ids = Post::GetAllStatusPermission();

        $financial_status_list      = [  5103300, 5103800 ];
        $status_option              = Option::get( "status_permission", $status_id, 5000, $allowed_status_ids );
        $financial_status_id_option = Option::get( "status_permission", $financial_status_id, 5103, $financial_status_list );


        if ( $status_id != 0 ) {
            $allowed_status_ids   = [];
            $allowed_status_ids[] = $status_id;
        }
        if ( $financial_status_id != 0 ) {
            $financial_status_list   = [];
            $financial_status_list[] = $financial_status_id;
        }

        $allowed_warehouse_ids = Warehouse::getAllowedWarehouse();
        $warehouse_option      = Option::get( "post_warehouse", $warehouse_id, 0, $allowed_warehouse_ids );
        // search
        if ( $warehouse_id != 0 ) {
            $allowed_warehouse_ids   = [];
            $allowed_warehouse_ids[] = $warehouse_id;
        }


        $list = Form::
        leftJoin( "financial_software_transfer_forms", "forms.id", "form_id" )->
        whereIn( "warehouse_id", $allowed_warehouse_ids )->
        whereIn( "forms.status_id", $allowed_status_ids )->
        when( count( $financial_status_list ) == 1, function ( $query ) use ( $financial_status_list ) {
            return $query->whereIn( "financial_software_transfer_forms.status_id", $financial_status_list );
        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );

        } )->
        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( "forms.code", "like", "%" . $search . "%" );
        } )->
        distinct( "forms.id" )->
        select( "forms.*" )->
        paginate( 30 );


        $order_by_Option = Option::OrderBy( "warehouse_input", $order_by );


        $result = $this->checkPermission( "wh.financial_software.transaction" );
        if ( $result != "" ) {
            return $result;
        }


        return view( $this->view_path . "form_list", compact( "list", "search", "warehouse_option", "status_option", "financial_status_id_option", "order_by_Option" ) );

    }

    public function transfer_from_list( Form $form, $page = 0 ) {

        return view( $this->view_path . "transfer_from_list", compact( "form", "page" ) );

    }

    public function log( $financial_software_transfer_form_id, $financial_trans_kind_type_id, $page = 0 ) {

        $result = $this->checkPermission( "wh.financial_software.transaction" );
        if ( $result != "" ) {
            return $result;
        }

        $financial_software_transfer_form = FinancialSoftwareTransferForm::find( $financial_software_transfer_form_id );
        if ( ! $financial_software_transfer_form_id ) {
            return back()->withErrors( "اطلاعات ثبت تراکنش مالی یافت نشد." );
        }
        $list                               = FinancialSoftwareTransKindLog::where( [
            "financial_software_transfer_form_id" => $financial_software_transfer_form->id,
        ] )->get();
        $financial_software_trans_kind_type = FinancialSoftwareTransKindType::find( $financial_trans_kind_type_id );

        return view( $this->view_path . "log", compact( "financial_software_transfer_form", "list", "financial_software_trans_kind_type", "page" ) );
    }

    public function review( $financial_software_transfer_form_id, $financial_software_trans_kind_type_id ) {
        $financial_software_transfer_form = FinancialSoftwareTransferForm::find( $financial_software_transfer_form_id );
        if ( ! $financial_software_transfer_form ) {
            return back()->withErrors( "اطلاعات فرم انتقال یافت نشد." );
        }
        $financial_software_trans_kind_type = FinancialSoftwareTransKindType::find( $financial_software_trans_kind_type_id );
        if ( ! $financial_software_trans_kind_type ) {
            return back()->withErrors( "اطلاعات ثبت تراکنش مالی یافت نشد." );
        }

        $status_name = $financial_software_trans_kind_type->caption_en;
        if ( $financial_software_transfer_form->$status_name == 5103800 ) {
            $financial_software_transfer_form->$status_name = 5103900;
            $financial_software_transfer_form->save();

            $log = FinancialSoftwareTransKindLog::create( [
                "form_id"                               => $financial_software_transfer_form->form->id,
                "financial_software_trans_kind_type_id" => $financial_software_trans_kind_type->id,
                "financial_software_transfer_form_id"   => $financial_software_transfer_form->id,
                "user_id"                               => Auth::id(),
                "status_id"                             => 5103900 // در انتظار بررسی ثبت
            ] );

            return back()->with( [ "success" => "درخواست بررسی مجدد با موفقیت ثبت گردید." ] );
        } else {
            return back()->withErrors( "درخواست بررسی مجدد نامعتبر است." );
        }
    }


    public function no_need_to_register( $financial_software_transfer_form_id, $financial_software_trans_kind_type_id ) {
        $financial_software_transfer_form = FinancialSoftwareTransferForm::find( $financial_software_transfer_form_id );
        if ( ! $financial_software_transfer_form ) {
            return back()->withErrors( "اطلاعات فرم انتقال یافت نشد." );
        }
        $financial_software_trans_kind_type = FinancialSoftwareTransKindType::find( $financial_software_trans_kind_type_id );
        if ( ! $financial_software_trans_kind_type ) {
            return back()->withErrors( "اطلاعات ثبت تراکنش مالی یافت نشد." );
        }

        $status_name = $financial_software_trans_kind_type->caption_en;
        if ( $financial_software_transfer_form->$status_name == 5103800 ) {
            $financial_software_transfer_form->$status_name = 5103100; // عدم نیاز به ثبت
            $financial_software_transfer_form->status_id= 5103100; // عدم نیاز به ثبت
            $financial_software_transfer_form->save();

            $log = FinancialSoftwareTransKindLog::create( [
                "form_id"                               => $financial_software_transfer_form->form->id,
                "financial_software_trans_kind_type_id" => $financial_software_trans_kind_type->id,
                "financial_software_transfer_form_id"   => $financial_software_transfer_form->id,
                "user_id"                               => Auth::id(),
                "status_id"                             =>  5103100 // عدم نیاز به ثبت
            ] );

            return back()->with( [ "success" => "درخواست بررسی مجدد با موفقیت ثبت گردید." ] );
        } else {
            return back()->withErrors( "درخواست بررسی مجدد نامعتبر است." );
        }
    }

    public function checkPermission( $button_name ) {


        $post_user = \Auth::user()->posts->first();

        // 615: "sales.dashboard.index";
        if ( $post_user->checkButtonPermission( $button_name ) ) {
            return null;

        } else {
            return back()->withErrors( "صفحه مورد نظر یافت نشد." );

        }


    }
}
