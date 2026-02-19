<?php

namespace App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\ProductionForm;

use App\Events\Form\PackingLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Http\Controllers\Controller;
use App\Models\Form\Packing\PackingForm;
use App\Models\Form\Packing\PackingFormItem;
use App\Models\Form\Packing\PackingFormLayer;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineStatus;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Production\ProductionForm;
use App\Models\Utility\Option;
use App\Models\Utility\QueueOfLargeOperation;
use App\Models\Utility\Setting;
use App\Models\Worker;
use Illuminate\Http\Request;
use App\Http\Controllers\GoodsKindProcess;
use Illuminate\Support\Facades\Auth;

class ExtractionItemController extends Controller {
    public static $info = [
        "route"         => "fabric_raw.jacquard.production_form.extraction_item.",
        "enable_status" => [ "از کلاس بالاتر گرفته می شود." ],
        "button"        => [ "caption" => "استخراج آیتم های فرم تولید", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.fabric_raw.jacquard.production_form.extraction_item.",

    ];
    var $view_path;
    var $route_path;
    var $production_form_view_route = "fabric_raw.production_form.view";

    public function __construct() {
        $this->route_path = ExtractionItemController::$info["route"];
        $this->view_path  = ExtractionItemController::$info["view_path"];
    }

    public function index( ProductionForm $production_form ) {

        $result = $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }

        // باید قبل از ورود به این صفحه پرینتر پیش فرض اوکی شده باشد تا پرینت ها به مشکل بر نخورند
        $worker = Worker::find(Auth::user()->id);
        if (!$worker->default_printer_id) {
            return redirect()->route("utility.printer.select_default_printer")->withErrors("لطفا پرینتر پیش فرض را انتخاب نمایید.");
        }

        $latest_production_form_item = $production_form->getLatestItem();
        if ( ! $latest_production_form_item ) {
            return back()->withErrors( "آخرین رکورد فرم تولید یافت نشد، لطفا با پشتیبانی تماس بگیرید." );
        }
        $allocation_item = MachineAllocation::where( [
            "production_id" => $latest_production_form_item->production_id,
            "machine_id"    => $production_form->machine_id
        ] )->first();

        if ( ! $allocation_item ) {
            return back()->withErrors( "تخصیص آخرین رکورد فرم تولید یافت نشد، لطفا با پشتیبانی تماس بگیرد." );
        }


        // بروزرسانی مقادیر فرم تولید
        $production_form->updateAmount();

        $packing_type_option                             = Option::get( "packing_type_extraction_separately", 0, $allocation_item->product->goods_kind_id );
        $production_form_item_lot_number_can_select_list = $this->production_form_item_lot_number( $production_form );

        if ( count( $production_form_item_lot_number_can_select_list ) == 0 ) {
            return back()->withErrors( "در حال حاضر امکان استخراج هیچ کدام از آیتم های فرم تولید وجود ندارد" );
        }
        if ( count( $packing_type_option["items"] ) == 0 ) {
            return back()->withErrors( "هیچ بسته بندی برای استخراج کالا وجود ندارد." );
        }

        return view( $this->view_path . "index", compact(
            "packing_type_option", "production_form_item_lot_number_can_select_list",
            "production_form"
        ) );

    }

    public function submit( Request $request, ProductionForm $production_form ) {

        $result = $this->checkPermission( $production_form );
        if ( $result != "" ) {
            return $result;
        }



        $end_of_packing_form_status=7007005; // در انتظار تحویل به انبار
        $has_grading_and_control = Setting::getIntegerValue( "has_grading_and_control" );
        if ( $has_grading_and_control ) {
            // کارخانه دارای واحد کنترل کیفیت می باشد
            $end_of_packing_form_status=7007026; // در انتظار کنترل کیفیت
        }

        $production_form_item_lot_number_list = $request->production_form_item_lot_number;

        $production_form_item_lot_number_can_select_list = $this->production_form_item_lot_number( $production_form );

        $packing_type = PackingType::find( $request->packing_type_id );
        foreach ( $production_form_item_lot_number_can_select_list as $item ) {

            if ( isset( $production_form_item_lot_number_list[ $item->id ]) && $item->amount > $packing_type->max_amount_of_production_form_separately ) {
                return back()->withErrors( " مقدار آیتم های بسته بندی انتخاب شده جهت استخراج بیش از حد مجاز می باشد." );
            }
        }


        if ( ! $packing_type ) {
            return back()->withErrors( "نوع بسته بندی نا معتبر است." );
        }
        if ( ! $packing_type->it_is_possible_extract_production_form_separately ) {
            return back()->withErrors( "نوع بسته بندی نا معتبر است." );
        }

        // اگر درجه بندی نداریم، باید درجه اصلی در رسته کالایی را انتخاب کنیم
        $main_degree = Degree::where( [
            "degree_type_id"   => 1, // درجه اصلی
            "goods_kind_id"    => 4, // پارچه خام
            "active_status_id" => 1200, // فعال
        ] )->first();
        if ( ! $main_degree ) {
            return back()->withErrors( "درجه اصلی در رسته کالایی پارچه خام مشخص نشده است." );
        }


        // ایجاد فرم بسته بندی
        $packing_form = PackingForm::create( [
            "packing_type_id" => $packing_type->id,
            "carrier_id"      => null,
            "status_id"       => $end_of_packing_form_status,
            "degree_id"       => $main_degree->id
        ] );
        event( new PackingLogEvent( $packing_form, 7007001 ) );

        // ثبت بسته بندی جدید
        foreach ( $production_form_item_lot_number_can_select_list as $production_form_item_lot_number ) {

            // اگر قبلا توسط استخراج آیتم ها استخراج شده است، دیگر نیاز نیست که بسته بندی شود.
            if ( isset( $production_form_item_lot_number_list[ $production_form_item_lot_number->id ] ) && ! $production_form_item_lot_number->getPackingFormItem() ) {


                // به ازای هر آیتم بسته بندی یک ردیف ایجاد می کنیم
                $sub_amount=$production_form_item_lot_number->amount *
                    $production_form_item_lot_number->production_form_item->product->weight;
                // واحد فرعی 2 برای پارچه
                $sub_amount2=$production_form_item_lot_number->production_form_item->product->frame_ratio_unit2?
                    floor($production_form_item_lot_number->amount/$production_form_item_lot_number->production_form_item->product->frame_ratio_unit2):null;


                $packing_form_item = PackingFormItem::create( [
                    "packing_form_id"                    => $packing_form->id,
                    "production_form_item_id"            => $production_form_item_lot_number->production_form_item->id,
                    "production_form_item_lot_number_id" => $production_form_item_lot_number->id,
                    "fabric_raw_grading_id"              => null,
                    "product_id"                         => $production_form_item_lot_number->production_form_item->product_id,
                    "lot_number_id"                      => $production_form_item_lot_number->lot_number_id,
                    "degree_id"                          => $main_degree->id,
                    "amount"                             => $production_form_item_lot_number->amount,
                    "amount_after_control"               => $production_form_item_lot_number->amount,
                    "final_amount"                       => $production_form_item_lot_number->amount,
                    "sub_amount"                         => $sub_amount,
                    "init_sub_amount"                         => $sub_amount,
                    "sub_amount2"                         =>$sub_amount2 ,
                    "status_id"                          => 7006003, // بسته بندی شده
                    "band_code"                          => $production_form_item_lot_number->production_form_item->band_code,
                    "version_code"=>$production_form_item_lot_number->production_form_item->version_code??null,
                ] );
                $packing_form_item->getCode( $production_form_item_lot_number->production_form_item->band_code, 1 );


                // به ازای هر آیتم اطلاعات باند و حامل را ذخیره می کنیم
                PackingFormLayer::create( [
                    "packing_form_item_id" => $production_form_item_lot_number->production_form_item->id,
                    "carrier_id"           => null,
                    "band_code"            => $production_form_item_lot_number->production_form_item->band_code,
                    "layer_code"           => 1
                ] );
            }
        }


// ثبت درخواست پرینت بسته بندی ها
        $data_print = [
            "packing_form_ids" => [$packing_form->id],
            "worker_id" => Auth::id(),
        ];
        QueueOfLargeOperation::AddToQueue($data_print, 300);


        event( new ProductionFormLogEvent(
            $production_form,
            7002018 // استخراج بخشی از فرم تولید
        ) );


        return redirect()->route( $this->production_form_view_route, $production_form )->with( [ "success" => "آیتم های فرم تولید با موفقیت استخراج شدند." ] );

    }

    public function checkPermission( ProductionForm $production_form ) {

        $result = DashboardController::checkPermissionConditions( $production_form, GoodsKindProcess\FabricRaw\ProductionForm\ExtractionItemController::$info );
        if ( ! $result["result"] ) {
            return back()->withErrors( $result["message"] );
        }

        return "";
    }

    public function production_form_item_lot_number( ProductionForm $production_form ) {
        $production_form_item_lot_number_can_select_list = [];

        foreach ( $production_form->items as $production_form_item ) {

            foreach ( $production_form_item->lot_numbers as $production_form_item_lot_number ) {
                // بررسی اینکه قبلا این آیتم استخراج نشده باشد
                if ( ! $production_form_item_lot_number->getPackingFormItem() ) {
                    $production_form_item_lot_number_can_select_list[] = $production_form_item_lot_number;
                }
            }


        }

        // قبلا آخرین آیتم را حذف می کردیم، الان آخرین اگر آخرین آیتم پایان بافت داشت اجازه استخراج آیتم را بدهد.
        if ( count( $production_form_item_lot_number_can_select_list ) > 0 ) {
            // تخصیص مربوط به آیتم خاتمه یافته شده است.
//            die( $production_form_item_lot_number->production_form_item->allocation->status_id);
            if( $production_form_item_lot_number->production_form_item->allocation->status_id != 5310020) {

                unset($production_form_item_lot_number_can_select_list[count($production_form_item_lot_number_can_select_list) - 1]);
            }
        }

        return $production_form_item_lot_number_can_select_list;
    }
}
