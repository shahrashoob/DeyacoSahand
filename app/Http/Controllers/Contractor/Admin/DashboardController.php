<?php

namespace App\Http\Controllers\Contractor\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\ContractorPackingForm;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Form;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Order\Order;
use App\Models\Post\PostStatus;
use App\Models\Production\Production;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller {
    //
    var $route_path = "contractor.admin.dashboard.";
    var $view_path = "contractor.admin.dashboard.";
    public static $perfix_production_status_code = 7008;

    public function index( Request $request ) {

        $allowed_status_ids = Status::where( "status_type_id", 7008 )->pluck( "id" )->toArray();
        if ( $request->waiting_status_id != 0 && ! in_array( $request->waiting_status_id, $allowed_status_ids ) ) {
            return back()->withErrors( "شما اجازه دسترسی به مشاهده کارت های پیمان با وضعیت انتخاب شده را ندارید" );
        }


        $goods_kind_id = 5;

        if ( $request->isMethod( 'post' ) ) {
            $search                 = $request->search;
            $order_by               = $request->order_by;
            $waiting_status_id      = $request->waiting_status_id;
            $goods_kind_property_id = $request->goods_kind_property_id;
            $order_search           = $request->order_search;
            $exit_form_status_id    = $request->exit_form_status_id;

        } else {
            $search                 = session( "search_contractor_card" );
            $order_by               = session( "order_by_contractor_card" ) ?? "production_cards.updated_at__desc";
            $waiting_status_id      = session( "waiting_status_id_contractor_card" );
            $goods_kind_property_id = session( "goods_kind_property_id_contractor_card" );
            $order_search           = session( "order_search_contractor_card" );
            $exit_form_status_id    = session( "order_search_exit_form_status_id" );

        }
        session( [
            "search_contractor_card"                 => $search,
            "order_by_contractor_card"               => $order_by,
            "waiting_status_id_contractor_card"      => $waiting_status_id,
            "goods_kind_property_id_contractor_card" => $goods_kind_property_id,
            "order_search_contractor_card"           => $order_search,
            "order_search_exit_form_status_id"       => $exit_form_status_id,
        ] );

        // search
        if ( $waiting_status_id != 0 ) {
            $allowed_status_ids   = [];
            $allowed_status_ids[] = $waiting_status_id;
        }

        // جستجوی بر اساس مشخصه کالا
        $product_ids = [];

        if ( $goods_kind_property_id ) {
            $product_ids   = GoodsKindPropertyValue::where( [
                "goods_kind_property_id" => $goods_kind_property_id,
                "value"                  => $search
            ] )->pluck( "product_id" )->toArray();
            $product_ids[] = 0;

        }

        $order_ids = [];

        if ( $order_search != "" ) {
            $order_ids = Order::where( "code", "like", "%" . $order_search . "%" )->
            orWhere( "series", "like", "%" . $order_search . "%" )->pluck( "id" );
        }


        $select = [
            "production_cards.number_in_carton",
            "production_cards.product_id",
            "serial",
            "production_cards.number",
            "production_cards.status_id",
            "production_cards.created_at",
            "production_cards.prioriry_id",
            "production_cards.waiting_status_id",
            "production_cards.id as id",
            "production_cards.order_id",
            "production_type_id"
        ];

        $list = Production::join( "products", "production_cards.product_id", "products.id" )->

        when( $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                return $query->where( "serial", "like", "%" . $search . "%" )
                             ->orWhere( "products.code", "like", "%" . $search . "%" )
                             ->orWhere( "products.caption", "like", "%" . $search . "%" );
            } );


        } )->

        when( $allowed_status_ids != [], function ( $query ) use ( $allowed_status_ids ) {

            return $query->where( function ( $query ) use ( $allowed_status_ids ) {
                $query->
                //whereIn( "production_cards.status_id", $allowed_status_ids )->
                OrwhereIn( "production_cards.waiting_status_id", $allowed_status_ids );
            } );

        } )->

        when( $exit_form_status_id != 0, function ( $query ) use ( $exit_form_status_id ) {
            $query->
            join( "machine_allocation", "machine_allocation.production_id", "production_cards.id" )->
            join( "product_request_forms", "product_request_forms.allocation_id", "machine_allocation.allocation_id" )->
            join( "product_request_form_form", "product_request_forms.id", "product_request_form_form.product_request_form_id" )->
            join( "forms", "forms.id", "product_request_form_form.form_id" )->
            where( "product_request_forms.applicant_type_id", 20 )->
            where( "forms.status_id", $exit_form_status_id );
        } )->
        when( $product_ids != [], function ( $query ) use ( $product_ids ) {
            return $query->whereIn( "production_cards.product_id", $product_ids );

        } )->
        when( $order_ids != [], function ( $query ) use ( $order_ids ) {
            return $query->whereIn( "production_cards.order_id", $order_ids );

        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );

        } )->
        select( $select )->

        addSelect( DB::raw( "production_cards.id as production_card_id" ) )->

        groupBy( "production_cards.id" );

        $list = $list->paginate( 50 );


        $order_by_Option = Option::OrderBy( "contractor_allocation", $order_by );

        $waiting_status_option = Option::get( "contractor_allocation_waiting_status", $waiting_status_id, 7008 );

        $property_option = Option::get( "get_property_by_goods_kind", $goods_kind_property_id, $goods_kind_id );

        $exit_form_status_option = Option::get( "status_in_ids", $exit_form_status_id, 0, Form::ExitFormStatus() );


        return view( $this->view_path . "index", compact( "waiting_status_option", "order_by_Option", "list", "search", "exit_form_status_option", "order_by_Option", "property_option", "order_search" ) );
    }

    public function view_card( Production $production,$back_url_type="" ) {

        // Check Permission
       $result= $this->checkPermission( $production );
       if($result){
           return $result;
       }

        $allocation_list = MachineAllocation::where( "production_id", $production->id )->get();

        return view( $this->view_path . "view_card", compact( "production", "allocation_list","back_url_type" ) );
    }

    public function log( MachineAllocation $contractor_allocation ) {


        $product_request_form_list = ProductRequestForm::where( "allocation_id", $contractor_allocation->allocation_id )->orderByDesc( "id" )->get();

        $contractor_packing_list = MachineAllocationPackingForm::where( [
            "contractor_id"         => $contractor_allocation->contractor_id,
            "machine_allocation_id" => $contractor_allocation->id
        ] )->
        where( "status_id", "!=", "7007006" )->//معلق
        get();

        $line_product_station = LineProductStation::
        whereNotNull( "contractor_operation_id" )->
        where( "contractor_id", $contractor_allocation->contractor_id )->
        where( "product_id", $contractor_allocation->product_id )->
        first();
        // گرفتن اولین BOM
        $bom         = BOM::where( "product_route_id", $line_product_station->product_route_id )->first();
        $product_bom = [];
        foreach ( $bom->items as $item ) {
            $line_product_station = LineProductStation::
            where( "product_id", $item->material_id )->
            first();
            if ( isset( $line_product_station ) ) {
                $product_bom[ $item->material->id ] = BOM::where( "product_route_id", $line_product_station->product_route_id )->first();
            }

        }


        return view( $this->view_path . "log", compact( "contractor_allocation", "bom", "product_bom", "product_request_form_list", "contractor_packing_list" ) );
    }


    public function view_form( ContractorAllocation $contractor_allocation, Form $form ) {

        $contractor = $contractor_allocation->contractor;

        return view( $this->view_path . "view_form", compact( "contractor_allocation", "form", "contractor" ) );
    }

    public function view_allocation() {

    }

    private function checkPermission( $production ) {
        if ( $production->product->supply_type_id != 3 ) {
            return back()->withErrors( "کارت از نوع پیمانی نمی باشد." );
        }
    }

    public static function checkPermissionConditions( Production $production, $info = false, $all_status = false ) {
        if ( $production->product->supply_type_id != 3 ) {
            return [
                "result"  => false,
                "message" => "کارت از نوع پیمانی نمی باشد.",
            ];
        }

        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_production_status_code . $value;
            }
            unset( $value );
            if ( ! $all_status && ! in_array( $production->waiting_status_id, $info["enable_status"] ) ) {
                return [
                    "result"     => false,
                    "message"    => "وضعیت کارت پیمان ".$production->serial()."(".$production->waiting_status->caption.")"." جهت عملیات نامعتبر است",
                    "error_type" => "for_waiting_status"
                ];
            }

        }

        return [
            "result" => true,
        ];

    }

}
