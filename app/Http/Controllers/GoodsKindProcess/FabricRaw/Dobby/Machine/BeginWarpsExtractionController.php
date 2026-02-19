<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\Machine;

use App\Events\Machine\MachineLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestForm;
use App\Models\GoodsKindProcess\Warps\Warps;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Product\ProductRequest\ProductRequestForm;
use App\Models\Utility\Option;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use function back;
use function event;
use function redirect;
use function view;

class BeginWarpsExtractionController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.machine.begin_warps_extraction.",
        "enable_status" => [ "040" ],
        "button"        => [ "caption" => "شروع استخراج چله", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.machine.begin_warps_extraction.",

    ];
    var $view_path;
    var $route_path;
    var $dashboard_route = "fabric_raw.machine.dashboard.";

    public function __construct() {
        $this->route_path = BeginWarpsExtractionController::$info["route"];
        $this->view_path  = BeginWarpsExtractionController::$info["view_path"];
    }


    public function index( Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }

        $shift_work_option = Option::get( "shift_work" );

        $warps_request = ProductRequestForm:: getLatestRequestForm( $machine->warehouse_id, 40,3 );

        $degree_option = Option::get( "degree", 0, 3 ); // چله

        return view( $this->view_path . "index", compact( "machine", "shift_work_option", "warps_request", "degree_option" ) );
    }

    public function submit( Request $request, Machine $machine ) {
        $result = $this->checkPermission( $machine );
        if ( $result != "" ) {
            return $result;
        }
        $message     = "";
        $degree_list = [];
        $amount_list=[];

// دریافت قطب ها
        $last_row_log = MachineLog::getLastLogWithContour($machine);

        if (
            isset( $last_row_log ) &&
            ! $last_row_log->checkMinContour(
                $request->contour_1_value,
                $request->contour_2_value,
                $request->contour_3_value,
                $request->contour_4_value,
                $request->contour_5_value )
        ) {
            $message .= "مقدار قطب ها به درستی وارد نشده است" . "<br/>";

        }

        $machineLog                        = new MachineLog();
        $machineLog->machine_event_type_id = 490;
        $machineLog->contour_1_value       = $request->contour_1_value;
        $machineLog->contour_2_value       = $request->contour_2_value;
        $machineLog->contour_3_value       = $request->contour_3_value;
        $machineLog->contour_4_value       = $request->contour_4_value;
        $machineLog->contour_5_value       = $request->contour_5_value;
        $machineLog->shift_work_id         = $request->shift_work_id;

        // بررسی درست بودن درجه چله ها
        $warps_request = ProductRequestForm:: getLatestRequestForm( $machine->warehouse_id, 40,3 );
        foreach ( $warps_request->items as $item ) {
            $id                       = "item_" . $item->id;
            $degree_list[ $item->id ] = Degree::find( $request->$id );
            if ( ! isset( $request->$id ) || ! $degree_list[ $item->id ] ) {
                $message .= "درجه چله ورودی" . $item->input_line_code . " نامعتبر است."."<br/>";
            }

        }

        if ( $message != "" ) {
            return back()->withErrors( $message );
        }

        event( new MachineLogEvent( $machine, $machineLog ,"",false,$last_row_log) );

        // بررسی مقدار باقی مانده چله با توجه به قطب ها
        foreach ( $warps_request->items as $item ) {

         //   $result= Warps::getRemainingAmountOfWarps($warps_request,$item);
            if(!$result["result"]){
                $message.=$result["message"]."<br/>";
            }
            $amount_list[$item->id]=$result;
        }

        if ( $message != "" ) {
            $machineLog->delete();
            return back()->withErrors( $message );
        }

        $machine->setStatus(
            null,
            53002,
            7003041, // در حال استخراج چله (دستور توقف)
            1730, //  استخراج چله (دستور توقف)
            "Fabric_Raw"
        );

        foreach ( $warps_request->items as $item ) {

            $form = Form::CreateFrom(
                [
                    "production_card_id"=>$item->production_id,
                    "user_id"      => Auth::user()->id,
                    "status_id"=>500000400, // در انتظار تحویل به انبار
                    "form_type_id" => 305,
                    "warehouse_id" => $degree_list[ $item->id ]->warehouse_id,
                    "trans_kind"   => 2, // دریافت از تولید
                    "ic"           => 1, // بافندگی
                    "applicant_type_id"=>10, // ماشین
                    "applicant_id"=>$machine->id
                ]
            );
            $form->getCode( "BW" ); //Back Warp
            FormItem::create([
                "form_id"=>$form->id,
                "product_id"=>$item->product_id,
                "amount"=> $amount_list[$item->id]["amount"],
                "sub_amount"=>$amount_list[$item->id]["sub_amount"],
                "packing_type_id"=>$item->warehouse_product->packing_type_id,
                "carrier_id"=>$item->warehouse_product->carrier_id,
                "degree_id"=>$degree_list[ $item->id ]->id,
                "lot_number_id"=>$item->warehouse_product->lot_number_id
            ]);
            event( new FormLogEvent($form) );

            // تغییر وضعیت حامل، به در انتظار تحویل به انبار
            $item->warehouse_product->carrier->SetStatus(5320004," استخراج چله -".$machine->fullCaption());

        }



          return redirect()->route( $this->dashboard_route . "view", compact( "machine" ) )->with( [ "success" => "عملیات با موفقیت انجام شد." ] );

    }

    public function checkPermission( Machine $machine ) {

        $result = DashboardController::checkPermissionConditions( $machine, BeginWarpsExtractionController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }
}
