<?php

namespace App\Models\LineProduct\Machine;

use App\Models\Contractor\Contractor;
use App\Models\Contractor\MachineAllocationLog;
use App\Models\Form\Packing\PackingForm;
use App\Models\LineProduct\LineProductStation;
use App\Models\LineProduct\Packing\PackingTypeLayer;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Station\Operation\StationSubOperation;
use App\Models\Order\Order;
use App\Models\Production\Production;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Option;
use App\Models\Utility\Status;
use App\Models\Worker;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineAllocation extends Model {
    use HasFactory;

    protected $table = "machine_allocation";
    protected $fillable = [
        "production_id",
        "machine_id",
        "contractor_id",
        "supplier_id",
        "order_id",
        "user_id",
        "status_id",
        "band_code",
        "product_id",
        "allocation_id",
        "allocation_amount",
        "allocation_sub_amount",
        "number_of_packing_form",
        "max_number_of_doffs",
        "amount_of_each_doffs",
        "number_of_doffs_done",
        "line_product_station_id",
        "version_code"
    ];

    /**
     * @param $type
     * @return int[]
     * این تابع برای گزارش های تولید و جاهایی که همه وضعیت های تخصیص مورد نیاز است، استفاده می شود.
     */
    public static function GetAllAllocationList($type="all")
    {
        return [
            5310010, 5310020, 5310040,
            5310102,5310103,5310104,5310105,5310106,5310107,5310108,5310109,
        ];
    }
    public function machine() {
        return $this->belongsTo( Machine::class );
    }
    public function contractor() {
        return $this->belongsTo( Contractor::class );
    }
    public function parent_allocation()
    {
        return $this->belongsTo(Allocation::class,"parent_allocation_id");
    }
    public function supplier() {
        return $this->belongsTo( Supplier::class );
    }
    public function order() {
        return $this->belongsTo( Order::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function allocation() {
        return $this->belongsTo( Allocation::class );
    }

    public function production() {
        return $this->belongsTo( Production::class );
    }

    public function reserve_productions() {
        return $this->belongsTo( Production::class )->where( "status_id", 5310040 );
    }
    // مسیر محصولی که در حال حاضر تخصیص بر روی آن در حال انجام است.
    public function line_product_station()
    {
        return $this->belongsTo(LineProductStation::class,"line_product_station_id");
    }
    public function worker() {
        return $this->belongsTo( Worker::class, "user_id", "id" );
    }

    public function logs() {
        return $this->hasMany( MachineAllocationLog::class, "machine_allocation_id" );
    }

    ################### date time
    public function get_date() {
        return $this->get_create_date();
    }

    public function get_time() {
       return $this->get_create_time();
    }

    public function get_datetime() {
        return $this->get_create_date_and_time();

    }

    public function get_create_date() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'Y/m/d' );
    }

    public function get_create_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i' );
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

//    ################### زمان تولید عملی و تثوری
    public function start_time() {
        if ( ! $this->production_start_date ) {
            return "";
        }

        return jdate( Carbon::parse( $this->production_start_date )->timestamp )->format( 'H:i Y/m/d ' );

    }


    public function getNumberOfBand() {
        return MachineAllocation::where( [
            "production_id" => $this->production_id,
            "machine_id"    => $this->machine_id,
            "allocation_id"=>$this->allocation_id
        ] )->count();
    }


    public function getRouteProperty( $property_id, $station_sub_operation_id_in_ic = 0 ) {
        $property = MachineProductProperties::find( $property_id );
        if ( ! $property ) {
            return [ "result" => false, "message" => "مشخصه مورد نظر تعریف نشده است.", "value" => null ];
        }
        $station_sub_operation_id=0;
        if ( $station_sub_operation_id_in_ic == 0 ) {
            return [ "result" => false, "message" => "نوع عملیات فرعی (ic) مشخص نشده است.", "value" => null ];
        }
        $station_sub_operation_id=StationSubOperation::
        where( "id_in_ic_system", $station_sub_operation_id_in_ic )->
        first()->id??0;
        if ( $station_sub_operation_id == 0 ) {
            return [ "result" => false, "message" => "نوع عملیات فرعی مشخص نشده است.", "value" => null ];
        }

        $value = $property->getValue( $this->machine->machine_type_id, $this->product_id, $station_sub_operation_id );

        return [ "result" => $value == null ? false : true, "value" => $value ];
    }

    public static function getProductionResult( $type, $layer, $machine_allocation, $packing_form_id ) {

        $goods_kind_id = $machine_allocation->product->goods_kind_id;
        $production    = $machine_allocation->production;

        switch ( $type ) {
            case 0:// بسته بندی جدید
                $degree_option = Option::get( "degree", 0, $goods_kind_id );;
                $packing_type_option=Option::get("production_packing_type",0,0,$production->packing_types);
                $first_packing_type = $production->packing_types()->first()->packing_type;
                if ( count( $production->packing_types ) == 0 ) {
                    return [
                        "result" => false,
                        "error"  => "نوع بسته بندی برای دستور پیمان ثبت نشده است، لطفا با پشتیبانی تماس بگیرید."
                    ];

                }

                if ( $production->packing_types()->first()->packing_type
                         ->layers()->count() == 1 ) {
                    return [
                        "result"             => true,
                        "view"               => "add_new_packing_1_layer",
                        "first_packing_type"=>$first_packing_type,
                        "packing_type_option"=>$packing_type_option,
                        "degree_option"      => $degree_option,
                        "layer"              => $layer,
                        "machine_allocation" => $machine_allocation,
                    ];
                    //   return view( $this->view_path . "", compact( "degree_option", "layer", "contractor_allocation", "contractor", "user_id" ) );

                } else {
                    return [
                        "result"             => true,
                        "view"               => "add_new_packing_n_layer",
                        "degree_option"      => $degree_option,
                        "packing_type_option"=>$packing_type_option,
                        "layer"              => $layer,
                        "machine_allocation" => $machine_allocation,
                    ];

                    //  return view( $this->view_path . "add_new_packing_n_layer", compact( "degree_option", "layer", "contractor_allocation", "contractor" ) );

                }

                break;
            case 1:
                $packing_form = PackingForm::find( $packing_form_id );
                if ( ! $packing_form ) {
                    return [
                        "result" => false,
                        "error"  => "فرم بسته بندی نامعتبر است."
                    ];
                    //  return back()->withErrors( "فرم بسته بندی نامعتبر است." );
                }
                if ( $packing_form->status_id != 7007006 ) {
                    return [
                        "result" => false,
                        "error"  => "وضعیت بسته جهت عملیات نامعتبر است."
                    ];
                    //   return back()->withErrors( "وضعیت بسته جهت عملیات نامعتبر است." );
                }
                if ( $layer == $packing_form->packing_type->layers()->count() ) {
                    return [
                        "result" => false,
                        "error"  => "امکان ثبت بسته بندی فرعی با لایه مورد نظر وجود ندارد."
                    ];
                    // return back()->withErrors( "امکان ثبت بسته بندی فرعی با لایه مورد نظر وجود ندارد." );
                }
                $layer_code = $packing_form->packing_type->layers()->where( "layer_code", $layer )->first();
                if ( ! $layer_code ) {
                    return [
                        "result" => false,
                        "error"  => "لایه سطح " . $layer . " برای بسته بندی یافت نشد."
                    ];
                    // return back()->withErrors( "لایه سطح " . $layer . " برای بسته بندی یافت نشد." );
                }

                if ( ! $packing_form->packing_type->first_packing_type ) {
                    return [
                        "result" => false,
                        "error"  => "بسته بندی لایه اول برای بسته بندی ثبت نشده است، لطفا با پشیتبانی تماس بگیرید."
                    ];
                    // return back()->withErrors( "بسته بندی لایه اول برای بسته بندی ثبت نشده است، لطفا با پشیتبانی تماس بگیرید." );
                }

                $list = PackingTypeLayer::where( [
                    "packing_type_id" => $packing_form->packing_type->first_packing_type_id
                ] )->get();

                $packing_type_product_option["items"]   = [];
                $packing_type_product_option["items"][] = [ "value" => 0, "text" => "لطفا یک مورد را انتخاب نمایید." ];
                $packing_type_product_option["value"]   = "";
                $packing_type_product_option["text"]    = "";

                // آیا آخرین لایه بسته بندی دارای حامل است یا خیر
                $has_number_ability = [];

                foreach ( $list as $item ) {
                    // اگر نوع حامل آخرین لایه بسته بندی برابر است با نوع حامل لایه بسته بندی 1، نوع بسته بندی جاری
                    if ( $item->packing_type->layers()->count() == $item->layer_code ) {
                        $packing_type_product_option["items"][]       = [
                            "value" => $item->packing_type_id,
                            "text"  => $item->packing_type->caption
                        ];
                        $has_number_ability[ $item->packing_type_id ] = $item->packing_type->carrier_type->has_number_ability ?? 0;
                    }
                }
                if ( count( $packing_type_product_option["items"] ) == 1 ) {
                    return [
                        "result" => false,
                        "error"  => "هیچ نوع بسته بندی برای کالا یافت نشد."
                    ];

                    return back()->withErrors( "هیچ نوع بسته بندی برای کالا یافت نشد." );
                }

                return [
                    "result"                      => true,
                    "view"                        => "add_new_packing",
                    "layer"                       => $layer,
                    "machine_allocation"          => $machine_allocation,
                    "has_number_ability"          => $has_number_ability,
                    "packing_type_product_option" => $packing_type_product_option,
                    "packing_form"                => $packing_form,
                ];
//                return view( $this->view_path . "add_new_packing",
//                    compact(
//                        "has_number_ability",
//                        "packing_type_product_option",
//                        "layer", "contractor_allocation", "contractor",
//                        "packing_form"
//                    )
//                );
                break;
            default:
                return [
                    "result" => false,
                    "error"  => "جهت ثبت بسته بندی لطفا با پشتیبانی تماس بگیرید."
                ];
            // return back()->withErrors( "جهت ثبت بسته بندی لطفا با پشتیبانی تماس بگیرید." );

        }
    }

    /**
     * در زمان ثبت تولید آیا اطلاعات بسته بندی برای ماشین - تخصیص دریافت شود؟
     * @return void
     */
    public static function allowGetPackingFormDetails(MachineAllocation $machine_allocation){
        return
            $machine_allocation->machine || // ثبت تولید کلی ماشین
            ($machine_allocation->contractor->get_packing_form_details ?? 0) || // ثبت تولید کلی پیمانکار
            ($machine_allocation->order->customer->get_packing_form_details??0) // ثبت تولید کلی تامین کنندگان ( کالای امانی)

            ;

    }
    public  function InputFromLoadingRequired(){
        if ($this->contractor) {
            return $this->contractor->input_form_loading_require;
        }
        if ($this->order) {
            return $this->order->customer->input_form_loading_require;
        }
        return false;

    }
    public function getTextOfThing($type)
    {
        // در این تابع لیست متن هایی که برای هر تخصیص می توانیم استفاده کنیم، (در ماژول ثبت تولید ) وجود دارد.
        switch ($type) {
            case "applicant_type":
                if ($this->machine) {

                } elseif ($this->contractor) {

                } elseif ($this->order) {

                }
                break;
            case "production_caption":
                if ($this->machine) {
                    return "کارت تولید";
                } elseif ($this->contractor) {
                    return "دستور پیمان";
                } elseif ($this->order) {
                    return "کارت تامین (کالای امانی)";
                }
                break;
            case "dashboard_caption":
                if ($this->machine) {
                    return "ماشین آلات";
                } elseif ($this->contractor) {
                    return "پیمانکاران";
                } elseif ($this->order) {
                    return "مشتریان";
                }
                break;
            case "fullCaption":
                if ($this->machine) {
                    return $this->machine->fullCaption();
                } elseif ($this->contractor) {
                    return $this->contractor->caption;
                } elseif ($this->order) {
                    return "سفارش " . $this->order->code;
                }
                break;
            case "warehouse_caption":
                if ($this->machine) {
                    return "انبار";
                } elseif ($this->contractor) {
                    return "کارفرما";
                } elseif ($this->order) {
                    return "پیمانکار";
                }
                break;
            case "btn_register":
                if ($this->machine) {
                    return "ثبت تولید";
                } elseif ($this->contractor) {
                    return "ثبت تولید";
                } elseif ($this->order) {
                    return "ثبت مواد اولیه";
                }
                break;
            case "final_button_and_send_text":
                if ($this->machine) {
                    return "ثبت نهایی تولید و ارسال به انبار";
                } elseif ($this->contractor) {
                    return "ثبت نهایی تولید و ارسال به کارفرما";
                } elseif ($this->order) {
                    return "ثبت نهایی تولید و ارسال به پیمانکار";
                }
                break;
            case "final_button_text":
                if ($this->machine) {
                    return "ثبت نهایی تولید";
                } elseif ($this->contractor) {
                    return "ثبت نهایی تولید";
                } elseif ($this->order) {
                    return "ثبت نهایی مواد اولیه";
                }
                break;
        }
        return $type;
        1 / 0;
    }
}
