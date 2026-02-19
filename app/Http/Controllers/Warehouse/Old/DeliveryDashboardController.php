<?php

namespace App\Http\Controllers\Warehouse;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeliveryDashboardController extends Controller {
    var $view_path = "warehouse.delivery_dashboard.";
    var $route_path = "wh.delivery_dashboard.";

    public function index() {

        $list = ProductRequestForm::
        orderByDesc( "id" )->
        paginate();
//      return  ProductRequestForm::orderByDesc("id")->first()->status;
        return view( $this->view_path . "index", compact( "list" ) );
    }

    public function show_form( WarpsRequestForm $warps_request_form ) {

        // فرم های در انتظار تایید درخواست کننده
        $waiting_for_confirm_form = WarpsRequestForm::where( "status_id", 7005004 )->first();

        if ( isset( $waiting_for_confirm_form ) && $warps_request_form->status_id == 7005001 ) {
            return back()->withErrors( "   فرم  " . $waiting_for_confirm_form->code . " <b>'در انتظار تایید درخواست کننده'</b> می باشد، تا زمانی که این فرم تایید نشود، امکان تحویل چله وجود ندارد." );
        }
        $lot_number_options = [];
        $min_of_lot_number  = count( $warps_request_form->items );
        foreach ( $warps_request_form->items as $item ) {
            $packing_type_ids                        = $item->warps_request_form_packing_types()->pluck( "packing_type_id" )->toArray();
            $lot_number_options[ $item->product_id ] = $this->get_lot_number_option( $item->product_id, $min_of_lot_number, $packing_type_ids );
        }


        return view( $this->view_path . "show_form", compact( "lot_number_options", "warps_request_form" ) );
    }

    public function get_lot_number_option( $product_id, $min_of_lot_number, $packing_type_ids ) {
        array_push( $packing_type_ids, - 1 );

        $carrier_list = Carrier::join( "carrier_product", "carrier_id", "carriers.id" )->
        where( [
            "product_id" => $product_id,
            "status_id"  => 5320002
        ] )->pluck( "carrier_id" );

        $lot_number_list = WarehouseProduct::where( [ "product_id" => $product_id ] )->
        whereIn( "carrier_id", $carrier_list )->
        whereIn( "packing_type_id", $packing_type_ids )->
        where( "output", 0 )->
        orderBy( "lot_number_id" )->
        orderByDesc( "id" )->
        take( 10000 )->
        get();

        // حذف رکوردهای تکراری در صورت وجود
        $warehouse_list = [];
        foreach ( $lot_number_list as $item ) {
            $warehouse_list[ $item->carrier_id ] = $item->id;
        }


        $lot_number_option ["items"] = [];
        $count                       = 0;
        $selected_list               = [];
        $lot_number_id               = 0;
        $lot_selected_count          = 0;
        foreach ( $lot_number_list as $item ) {

            if ( $warehouse_list[ $item->carrier_id ] != $item->id ) {
                continue;
            }

            if ( ! isset( $selected_list [ $item->carrier->id ] ) ) {
                if ( $lot_number_id != $item->lot_number_id ) {
                    $lot_number_id      = $item->lot_number_id;
                    $lot_selected_count = $count;
                    if ( $lot_selected_count >= $min_of_lot_number ) {
                        break;
                    }
                }

                $lot_number_option ["items"] []       =
                    [
                        "id"    => $item->id,
                        "text"  => $item->carrier->getCaption() . " - " . ( $item->packing_type->caption ?? "***" ),
                        "value" => $item->id
                    ];
                $selected_list [ $item->carrier->id ] = 1;

                $count ++;
            }

        }

        return $lot_number_option;
    }

    public function get_lot_number_should_be_selected( $product_id, $min_of_lot_number ) {
        $carrier_list = Carrier::join( "carrier_product", "carrier_id", "carriers.id" )->where( [
            "product_id" => $product_id,
            "status_id"  => 5320002
        ] )->pluck( "carrier_id" );

        $lot_number_list = WarehouseProduct::where( [ "product_id" => $product_id ] )->
        whereIn( "carrier_id", $carrier_list )->
        orderBy( "lot_number_id" )->get();


        $lot_number_option = [];
        $count             = 0;
        foreach ( $lot_number_list as $item ) {
            if ( $count <= $min_of_lot_number ) {
                $lot_number_option [] = $item->lot_number_id;
            }
            $count ++;
        }

        $lot_number_option = collect( $lot_number_option )->sort()->take( $min_of_lot_number );
        $large_id          = $lot_number_option->max();
        $lot_number_option = $lot_number_option->values()->all();

//delete element in array by value $large_id
        if ( ( $key = array_search( $large_id, $lot_number_option ) ) !== false ) {
            unset( $lot_number_option[ $key ] );
        }

        return $lot_number_option;
    }


    public function delivery( Request $request, ProductRequestForm $product_request_form ) {

//return $request->all();
        $lot_number_should_be_check_equals = $product_request_form->lot_number_should_be_check_equals();
        $carrier_list                      = [];
        $warps_lot_number_id               = 0;
        foreach ( $warps_request_form->items as $item ) {

            $id = "warehouse_product_id_" . $item->id;
            if ( ! isset( $request->$id ) ) {
                return back()->withErrors( "لطفا شماره غلطک را برای همه موارد انتخاب کنید" );
            }

            $warehouse_product[ $item->id ] = WarehouseProduct::find( $request->$id );

            if ( isset( $carrier_list[ $warehouse_product[ $item->id ]->carrier_id ] ) ) {
                return back()->withErrors( "شماره غلطک باید فقط برای یک باند انتخاب گردد." );
            }
            $carrier_list[ $warehouse_product[ $item->id ]->carrier_id ] = $id;

            $ic = $warps_request_form->machine->machine_type->ic ?? "";
            if ( $ic == "" ) {
                return back()->withErrors( "مرکز هزینه نا معتبر است" );
            }

            if (
                $warps_lot_number_id != 0 &&
                $lot_number_should_be_check_equals &&
                $warps_lot_number_id != $warehouse_product[ $item->id ]->lot_number_id ) {
                return back()->withErrors( "با توجه به باندهای خروجی و باند ورودی ماشین، همافت چله ها نمی توانند یکسان باشند." );
            }

            $warps_lot_number_id = $warehouse_product[ $item->id ]->lot_number_id;
        }

        // بررسی اینکه کوچکترین لات ها انتخاب شده باشند.
//        $min_of_lot_number = count( $warps_request_form->items );
//        foreach ( $warps_request_form->items as $item ) {
//
//            return $lot_number_options[ $item->product_id ] = $this->get_lot_number_should_be_selected( $item->product_id, $min_of_lot_number );
//        }


// ثبت فرم خروج از انبار
        $form = Form::CreateFrom( [
            "order_id"      => 0,
            "order_list_id" => 0,
            "user_id"       => Auth::user()->id,
            "form_type_id"  => 303,
            "trans_kind"    => 8,
            "ic"            => $ic,
            "status_id"     => 500000500
        ] );

        $form->getCode( "WEF/" );// Warehouse Exit Form
        foreach ( $warps_request_form->items as $item ) {
            $form_item = FormItem::create( [
                "form_id"         => $form->id,
                "product_id"      => $item->product_id,
                "amount"          => $warehouse_product[ $item->id ]->input,
                "sub_amount"      => $warehouse_product[ $item->id ]->sub_amount,
                "carrier_id"      => $warehouse_product[ $item->id ]->carrier_id,
                "degree_id"       => $warehouse_product[ $item->id ]->degree_id,
                "lot_number_id"   => $warehouse_product[ $item->id ]->lot_number_id,
                "packing_type_id" => $warehouse_product[ $item->id ]->packing_type_id
            ] );

            $id                         = "warehouse_product_id_" . $item->id;
            $item->warehouse_product_id = $request->$id;
            $item->save();

// آپدیت اطلاعات فرم درخواست چله
            $warps_request_form->status_id = WarpsRequestForm::$perfix_status_code . "004";
            $warps_request_form->form_id   = $form->id;
            $warps_request_form->save();
// اپدیت اطلاعات فرم انبار
            $form->warehouse_id = $warehouse_product[ $item->id ]->warehouse_id;
            $form->save();
        }


// ویرایش وضعیت ماشین اگر در انتظار چله است
        $machine = $warps_request_form->machine;

        if ( $machine->production_status_id == "7003019" ) {
            $machine->on_status_id          = 53002;
            $machine->machine_off_reason_id = 1606;
            // با توجه به نوع ماژول ماشین، ممکن است وضعیت بعدی ماشین تغییر کند.
            $machine->production_status_id  = MachineModuleType::getChecklist( $machine->machine_type->machine_module_type_id, "warps_delivery_7003019" );
            $machine->save();
        }
        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 255;
        event( new MachineLogEvent( $machine, $machineLog ) );

        event( new WarpsRequestFormLogEvent( $warps_request_form ) );

        return redirect()->route( $this->route_path . "index" )->
        with( [ "success" => "عملیات با موقیت انجام شد. " ] );
    }

    public function log( WarpsRequestForm $warps_request_form ) {
        return view( $this->view_path . "log", compact( "warps_request_form" ) );
    }
}
