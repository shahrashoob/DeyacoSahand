<?php

namespace App\Http\Controllers\Contractor\Panel;

use App\Events\Form\PackingLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Contractor\ContractorAllocation;
use App\Models\Contractor\MachineAllocationPackingForm;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\Form\Packing\PackingForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function back;
use function event;
use function redirect;
use function view;

class SendToEmployerController extends Controller {

    var $view_path = "contractor.panel.send_to_employer.";
    var $route_path = "contractor.panel.send_to_employer.";
    var $dashboard_route = "contractor.panel.dashboard.";


    public function __construct() {

    }


    public function index( ContractorAllocation $contractor_allocation ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( $result != "" ) {
            return $result;
        }

        $contractor              = $contractor_allocation->contractor;
        $contractor_packing_list = MachineAllocationPackingForm::where( [
            "contractor_id"         => $contractor_allocation->contractor_id,
            "machine_allocation_id" => $contractor_allocation->id,
            "status_id"             => "7007008" // در انتظار ارسال محصول
        ] )->
        get();

        return view( $this->view_path . "index", compact( "contractor_packing_list", "contractor_allocation", "contractor" ) );

    }

    public function submit( Request $request, ContractorAllocation $contractor_allocation ) {

        $result = $this->checkPermission( $contractor_allocation );
        if ( $result != "" ) {
            return $result;
        }

        // حذف بسته بندی های معلق api که دیگر استفاده نشده اند.
        MachineAllocationPackingForm::where( [
            "contractor_id"         => $contractor_allocation->contractor_id,
            "machine_allocation_id" => $contractor_allocation->id,
            "status_id"             => "7007011" // معلق -api
        ] )->
        delete();

        if ( ! isset( $request->data ) || count( $request->data ) == 0 ) {
            return back()->withErrors( "لطفا حداقل یک بسته را انتخاب نمایید." );
        }

        $packing_ids             = array_keys( $request->data );
        $contractor_packing_list = MachineAllocationPackingForm::where( [
            "contractor_id"         => $contractor_allocation->contractor_id,
            "machine_allocation_id" => $contractor_allocation->id,
            "status_id"             => "7007008" // در انتظار ارسال محصول
        ] )->
        whereIn( "id", $packing_ids )->
        get();


        foreach ( $contractor_packing_list as $contractor_packing_item ) {

//            foreach ( $packing_form->items as $item ) {
//                if ( $item->lot_number->nosa_code == null && ! $lot_nomber_is_allowed_setting ) {
//                    return redirect()->route( $this->route_path . "get_nosa_code", $packing_form );
//                }
//            }

            if ( ( $contractor_packing_item->packing_form->getDegree() ) == null ) {
                return back()->withErrors( "انبار مرتبط با درجه کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد." );
            }
            $warehouse_id = ( $contractor_packing_item->packing_form->getDegree() )->warehouse_id ?? null;

//            foreach ( $contractor_packing_item->packing_form->sub_packing as $sub_packing_item ) {
//                if ( ! $sub_packing_item->packing_form->getDegree() ) {
//                    return back()->withErrors( "انبار مرتبط با درجه کالا یافت نشد، لطفا با پشتیبانی تماس بگیرد." );
//                }
//            }
        }




        // ایجاد یک فرم تولید برای کل بسته ها
        $form = Form::CreateFrom( [
            "order_id"           => 0,
            "order_list_id"      => 0,
            "production_card_id" => $contractor_allocation->production_id,
            "user_id"            => Auth::user()->id,
            "form_type_id"       => 304,
            "trans_kind"         => 3, // دریافت از پیمان کار
            "warehouse_id"       => $warehouse_id,
            "status_id"          => 500000410, // در انتظار تایید انبار
            "ic"=>$contractor_allocation->contractor->getIC()
        ] );

        $form->getCode(  );


        foreach ( $contractor_packing_list as $contractor_packing_item ) {

            // ثبت آیتم های بسته بندی
            foreach ( $contractor_packing_item->packing_form->items as $item ) {
                FormItem::create( [
                    "form_id"              => $form->id,
                    "packing_form_item_id" => $item->id,
                    "product_id"           => $item->product_id,
                    "amount"               => $item->amount,
                    "sub_amount"           => $item->sub_amount,
                    "carrier_id"           => $item->packing_form->carrier_id,
                    "degree_id"            => $item->degree_id,
                    "lot_number_id"        => $item->lot_number_id,
                    "packing_type_id"        => $item->packing_form->packing_type_id,
                    "description"=>"دریافت کالا با کد بسته بندی " . ($item->getCode())
                ] );
                $item->status_id = 7007009; //  در انتظار تایید دریافت محصول
                $item->save();


            }

//            // ثبت آیتم های بسته بندی های فرعی
//            foreach ( $contractor_packing_item->packing_form->sub_packing as $sub_packing_item ) {
//
//                foreach ( $sub_packing_item->packing_form->items as $item ) {
//                    FormItem::create( [
//                        "form_id"              => $form->id,
//                        "packing_form_item_id" => $item->id,
//                        "product_id"           => $item->product_id,
//                        "amount"               => $item->amount,
//                        "sub_amount"           => $item->sub_amount,
//                        "carrier_id"           => $item->packing_form->carrier_id,
//                        "degree_id"            => $item->degree_id,
//                        "lot_number_id"        => $item->lot_number_id
//                    ] );
//                    $item->status_id = 7007009; //  در انتظار تایید دریافت محصول
//                    $item->save();
//                    $amount += $item->amount;
//                }
//
//                $sub_packing_item->packing_form->status_id = 7007009; //  در انتظار تایید دریافت محصول
//                $sub_packing_item->packing_form->form_id   = $form->id;
//                $sub_packing_item->packing_form->save();
//                event( new PackingLogEvent( $sub_packing_item->packing_form, 7007005 ) );
//
//                // تغییر وضعیت حامل
//                if ( $sub_packing_item->packing_form->carrier ) {
//                    $sub_packing_item->packing_form->carrier->SetStatus( 5320007,null,5320102,null,null,$contractor_allocation->contractor_id );
//                }
//            }


            event( new FormLogEvent( $form ) );

            $contractor_packing_item->packing_form->status_id = 7007009; //  در انتظار تایید دریافت محصول
            $contractor_packing_item->packing_form->form_id   = $form->id;
            $contractor_packing_item->packing_form->save();
            event( new PackingLogEvent( $contractor_packing_item->packing_form, 7007005 ) );

            // تغییر وضعیت حامل
            if ( $contractor_packing_item->packing_form->carrier ) {
                $contractor_packing_item->packing_form->carrier->SetStatus( 5320007 ,null,5320102,null,null,$contractor_allocation->contractor_id);
            }

            $contractor_packing_item->status_id = 7007005;
            $contractor_packing_item->save();
        }






        return redirect()->route( $this->dashboard_route . "view", compact( "contractor_allocation" ) )->with(["success"=>"بسته ها با موفقیت ارسال شد."]);
    }

    public function get_nosa_code( PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }

        return view( $this->view_path . "get_nosa_code", compact( "packing_form" ) );
    }

    public function submit_nosa_code( Request $request, PackingForm $packing_form ) {

        $result = $this->checkPermission( $packing_form );
        if ( $result != "" ) {
            return $result;
        }
        foreach ( $packing_form->items as $item ) {
            if ( $item->lot_number->nosa_code == null ) {
                $nosa_code = "nosa_code_" . $item->id;
                if ( ! $request->$nosa_code != "" ) {
                    return back()->withErrors( "لطفا کد نوسا برای همه کالا ها را وارد نمایید." );
                } else {
                    $item->lot_number->nosa_code = $request->$nosa_code;
                    $item->lot_number->save();
                }
            }
        }

        return redirect()->route( $this->dashboard_route . "view", $packing_form )->with( [ "success" => "کد نرم افزار مالی ثبت گردید." ] );

    }

    public function checkPermission( ContractorAllocation $contractor_allocation ) {

        $result = DashboardController::checkPermissionConditions( $contractor_allocation, RegisterProductionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
