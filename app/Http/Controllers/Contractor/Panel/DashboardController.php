<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\ContractorPackingForm;
use App\Models\Contractor\ContractorPost;
use App\Models\Form\Form;
use App\Models\Form\FormGeneralItem;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Product\BOM\BOM;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestFormForm;
use App\Models\Order\Order;
use App\Models\Production\Production;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Option;
use App\Models\Utility\Transport\Transport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DashboardController extends Controller {
    var $view_path = "contractor.panel.dashboard.";
    var $route_path = "contractor.panel.dashboard.";
    public static $perfix_status_code = "5310";

    public function index( Request $request ) {
        $result = $this->checkPermission();
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        $contractor_ids = ContractorPost::whereIn( "post_id", $result["post_ids"] )->pluck( "contractor_id" )->toArray();

        $goods_kind_id = 5;

        if ( $request->isMethod( 'post' ) ) {
            $search                 = $request->search;
            $order_by               = $request->order_by;
            $waiting_status_id      = $request->waiting_status_id;
            $goods_kind_property_id = $request->goods_kind_property_id;
            $order_search           = $request->order_search;

        } else {
            $search                 = session( "search_contractor_card" );
            $order_by               = session( "order_by_contractor_card" ) ?? "production_cards.updated_at__desc";
            $waiting_status_id      = session( "waiting_status_id_contractor_card" );
            $goods_kind_property_id = session( "goods_kind_property_id_contractor_card" );
            $order_search           = session( "order_search_contractor_card" );

        }
        session( [
            "search_contractor_card"                 => $search,
            "order_by_contractor_card"               => $order_by,
            "waiting_status_id_contractor_card"      => $waiting_status_id,
            "goods_kind_property_id_contractor_card" => $goods_kind_property_id,
            "order_search_contractor_card"           => $order_search,
        ] );


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

//       return $order_search;

        $list = ContractorAllocation::join( "production_cards", "production_cards.id", "production_id" )->
        join( "products", "production_cards.product_id", "products.id" )->
        whereIn( "contractor_id", $contractor_ids )->
        where( "supply_type_id", 3 )->
        when( ! $goods_kind_property_id && $search != "", function ( $query ) use ( $search ) {
            return $query->where( function ( $query ) use ( $search ) {
                $query->where( "serial", "like", "%" . $search . "%" );
                $query->orWhere( "products.code", "like", "%" . $search . "%" );
                $query->orWhere( "products.caption", "like", "%" . $search . "%" );
            } );

        } )->
        when( $order_by != "", function ( $query ) use ( $order_by ) {
            $order_by = Str::of( $order_by )->explode( "__" );

            return $query->orderBy( $order_by[0], $order_by[1] );

        } )->
//        when( $select != [], function ( $query ) use ( $select ) {
//            $select[] = "production_cards.id as id";
//
//            return $query->select( $select );
//        } )->
        when( $waiting_status_id != 0, function ( $query ) use ( $waiting_status_id ) {
            return $query->where( function ( $query ) use ( $waiting_status_id ) {
                $query->where( "machine_allocation.status_id", $waiting_status_id );
            } );
        } )->
        when( $product_ids != [], function ( $query ) use ( $product_ids ) {
            return $query->whereIn( "production_cards.product_id", $product_ids );
        } )->
        when( $order_ids != [], function ( $query ) use ( $order_ids ) {
            return $query->whereIn( "production_cards.order_id", $order_ids );

        } )->
        select( [
            "allocation_id",
            "production_id",
            "contractor_id",
            "machine_allocation.product_id",
            "allocation_amount",
            "machine_allocation.status_id"
        ] )->
        addSelect( DB::raw( "machine_allocation.id as id" ) )->
        paginate( 50 );;

        $order_by_Option = Option::OrderBy( "contractor_allocation", $order_by );

        $waiting_status_option = Option::get( "status", $waiting_status_id, 5311 );

        $property_option = Option::get( "get_property_by_goods_kind", $goods_kind_property_id, $goods_kind_id );


        return view( $this->view_path . "index", compact( "list", "order_by_Option", "waiting_status_option", "property_option", "search", "order_search" ) );
    }

    public function view( ContractorAllocation $contractor_allocation ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

if(!$contractor_allocation->product->unit){
    return back()->withErrors("واحد کالا برای ".$contractor_allocation->product->caption." مشخص نشده است.");
}
        $contractor = $contractor_allocation->contractor;

        $product_request_form_list = ProductRequestForm::where( "allocation_id", $contractor_allocation->allocation_id )->orderByDesc( "id" )->get();

        $contractor_packing_list = $contractor_allocation->getContractorPackingForm();

        $production_amount = ProductionFormItem::
        where( "production_id", $contractor_allocation->production->id )->
        sum( "final_amount" );

        $line_product_station = LineProductStation::
        whereNotNull( "contractor_operation_id" )->
        where( "contractor_id", $contractor_allocation->contractor_id )->
        where( "product_id", $contractor_allocation->product_id )->
        first();

        // گرفتن اولین BOM
        $bom = BOM::where( "product_route_id", $line_product_station->product_route_id )->first();

        $product_bom = [];
        foreach ( $bom->items as $item ) {
            $line_product_station = LineProductStation::
            where( "product_id", $item->material_id )->
            first();
            if ( $line_product_station ) {
                $product_bom[ $item->material->id ] = BOM::where( "product_route_id", $line_product_station->product_route_id )->first();
            }
        }


        // لیست فرم های پیمانکار
        $form_general_item_list = FormGeneralItem::
        where( "machine_allocation_id", $contractor_allocation->id )->
        whereNotNull( "form_id" )->
        get();

        $form_ids       = Form::
        join( "form_general_item", "forms.id", "form_id" )->
        where( "machine_allocation_id", $contractor_allocation->id )->
        pluck( "form_id" )->toArray();
        $form_ids[]     = - 1;
        $transport_list = Transport::join( "transport_form", "transports.id", "transport_id" )->
        whereIn( "form_id", $form_ids )->
        select( "transports.*" )->
        get();


        return view( $this->view_path . "view", compact( "bom", "product_bom", "contractor", "contractor_allocation", "contractor_packing_list", "product_request_form_list", "production_amount", "transport_list", "form_general_item_list" ) );


    }

    public function view_packing( ContractorAllocation $contractor_allocation, PackingForm $packing_form ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }
        if ( $packing_form->status_id == 7007006 ) {
            return back()->withErrors( "وضعیت بسته  نامعتبر است." );
        }
        $contractor = $contractor_allocation->contractor;

        return view( $this->view_path . "view_packing_item", compact( "contractor", "contractor_allocation", "packing_form" ) );


    }

    public function view_form( ContractorAllocation $contractor_allocation, Form $form ) {
        $result = $this->checkPermission( $contractor_allocation );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }
        $contractor = $contractor_allocation->contractor;

        return view( $this->view_path . "view_form", compact( "contractor_allocation", "form", "contractor" ) );
    }

    public function view_product_request_form( ContractorAllocation $contractor_allocation, ProductRequestForm $product_request_form ) {
        $result = $this->checkPermission( $contractor_allocation );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }
        $contractor = $contractor_allocation->contractor;

        return view( $this->view_path . "view_product_request_form", compact( "contractor_allocation", "product_request_form", "contractor" ) );

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

    public static function checkPermissionConditions( $contractor_allocation, $info = false, $all_status = false ) {

        if ( $contractor_allocation->production->product->supply_type_id != 3 ) {
            return [
                "result"  => false,
                "message" => "کارت از نوع پیمانی نمی باشد.",
            ];
        }

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


        if ( $info != false ) {
            foreach ( $info["enable_status"] as &$value ) {
                $value = DashboardController::$perfix_status_code . $value;
            }
            unset( $value );
            if ( ! $all_status && ! in_array( $contractor_allocation->status_id, $info["enable_status"] ) ) {
                return [
                    "result"     => false,
                    "message"    => "وضعیت کارت پیمان جهت عملیات نامعتبر است",
                    "error_type" => "for_waiting_status"
                ];
            }

        }

        return [
            "result"   => true,
            "post_ids" => $post_ids
        ];
    }
}
