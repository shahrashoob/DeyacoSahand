<?php

namespace App\Models\LineProduct\Machine\Maintenance;

use App\Events\Machine\MachineLogEvent;
use App\Events\Machine\MaintenanceLogEvent;
use App\Models\LineProduct\GoodsKindPropertyValue;
use App\Models\LineProduct\Machine\Allocation;
use App\Models\LineProduct\Machine\Fault\CurrentMachineFault;
use App\Models\LineProduct\Machine\Fault\MachineFault;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\Utility\Status;
use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;


class Maintenance extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "maintenance";
    protected $fillable = [
        "machine_id",
        "maintenance_type_id",
        "status_id",
        "description",
        "other_id"
    ];

    public function machine() {
        return $this->belongsTo( Machine::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function maintenance_type() {
        return $this->belongsTo( MaintenanceType::class );
    }

    public function logs() {
        return $this->hasMany( MaintenanceLog::class );
    }

    public function getCode() {
        if ( isset( $this->code ) && $this->code != "" ) {
            return $this->code;
        }

        $this->code = "DCEM/" . ( 1000 + $this->id );
        $this->save();

        return $this->code;
    }

    public function get_create_date_and_time() {
        return jdate( Carbon::parse( $this->created_at )->timestamp )->format( 'H:i Y/m/d ' );

    }

    public static function AddNew(
        $allocation, Machine $machine, $maintenance_type_id,
        $value1, $value2, $value3, $value4, $status_id,
        $maintenance_status_id, $other_id
    ) {

        $maintenance_type = MaintenanceType::find( $maintenance_type_id );
        $description      = $maintenance_type->description_template;
        $description      = Str::replace( "#*value1*#", $value1, $description );
        $description      = Str::replace( "#*value2*#", $value2, $description );
        $description      = Str::replace( "#*value3*#", $value3, $description );
        $description      = Str::replace( "#*value4*#", $value4, $description );
        $maintenance      = Maintenance::create( [
            "allocation_id"       => $allocation->id ?? null,
            "machine_id"          => $machine->id,
            "maintenance_type_id" => $maintenance_type_id,
            "status_id"           => $status_id, // در انتظار شروع پیش فرض
            "description"         => $description,
            "other_id"            => $other_id ?? null
        ] );
        $maintenance->getCode();
        event( new MaintenanceLogEvent( $maintenance, 6003101 ) );
        if ( $maintenance_status_id ) {
            $machine->maintenance_status_id = $maintenance_status_id;
            $machine->save();
        }

        return $maintenance;
    }

    public static function UpdateMaintenance( Maintenance $maintenance, $event_type ) {
        switch ( $event_type ) {
            case "start":
                $maintenance->status_id = 6003002; // در حال انجام
                $maintenance->save();
                event( new MaintenanceLogEvent( $maintenance, 6003102 ) );

                return [ "result" => true, "maintenance" => $maintenance ];
                break;

            case "end":

                switch ( $maintenance->maintenance_type_id ) {
                    case "100": // تغییر تراکم
                        $number_open = Maintenance::where( "machine_id", $maintenance->machine_id )->
                        where( "status_id", "!=", 6003003 )-> // خاتمه یافته
                        count();

                        if ( $number_open == 0 ) {
                            $machine                        = Machine::find( $maintenance->machine_id );
                            $machine->maintenance_status_id = 6002001; // عادی
                            $machine->save();

                            $machineLog                        = new MachineLog();
                            $machineLog->machine_event_type_id = 470;


                        }
                        event( new MaintenanceLogEvent( $maintenance, 6003103 ) );

                        return [ "result" => true, "maintenance" => $maintenance ];

                        break;


                    case "200": // رفع نقص
                        $machine_fault = MachineFault::find( $maintenance->other_id );

                        if ( ! $machine_fault ) {
                            return [ "result" => false, "error" => "مقدار نقص ماشین معتبر نمی باشد." ];
                        }
                        $list = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"        => 6004002 // تایید شده
                        ] )->get();
                        if ( count( $list ) == 0 ) {
                            return [
                                "result" => false,
                                "error"  => " نقص برای ماشین فعال نمی باشد، لطفا با پشتیبانی تماس بگیرید."
                            ];
                        }

                        // آیا رفع نقص نیاز به تایید دارد؟
                        if ( $machine_fault->need_to_confirmation_for_fix ) {
                            $list                   = CurrentMachineFault::where( [
                                "machine_id"       => $maintenance->machine_id,
                                "machine_fault_id" => $maintenance->other_id,
                                "status_id"        => 6004002 // تایید شده
                            ] )->
                            update( [
                                "status_id" => 6004003 // در انتظار تایید رفع نقص
                            ] );
                            $maintenance->status_id = 6003005; // در انتظار تایید درخواست کننده
                            $maintenance->save();

                        } else {
                            $list = CurrentMachineFault::where( [
                                "machine_id"       => $maintenance->machine_id,
                                "machine_fault_id" => $maintenance->other_id,
                                "status_id"        => 6004002 // تایید شده
                            ] )->update( [
                                "active_status_id" => 1210,
                                "status_id"        => 6004004 // رفع نقص شده
                            ] );

                            $maintenance->status_id = 6003003; // خاتمه یافته
                            $maintenance->save();
                        }
                        event( new MaintenanceLogEvent( $maintenance, 6003103 ) );

                        return [ "result" => true, "maintenance" => $maintenance ];

                        break;
                }
                break;

            case "confirm":

                switch ( $maintenance->maintenance_type_id ) {
                    case "100": // تغییر تراکم
                        1 / 0;

                        break;


                    case "200": // رفع نقص
                        $machine_fault = MachineFault::find( $maintenance->other_id );

                        if ( ! $machine_fault ) {
                            return [ "result" => false, "error" => "مقدار نقص ماشین معتبر نمی باشد." ];
                        }
                        $list = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"        => 6004001 // در انتظار تایید
                        ] )->get();
                        if ( count( $list ) == 0 ) {
                            return [
                                "result" => false,
                                "error"  => " نقص برای ماشین یافت نشد، لطفا با پشتیبانی تماس بگیرید."
                            ];
                        }

                        $list                   = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"        => 6004001 // در انتظار تایید
                        ] )->
                        update( [
                            "active_status_id" => 1200, // تایید شده
                            "status_id"        => 6004002 // تایید شده
                        ] );

                        $maintenance->status_id = 6003001; // در انتظار شروع
                        $maintenance->save();
                        event( new MaintenanceLogEvent( $maintenance, 6003104 ) );

                        return [ "result" => true, "maintenance" => $maintenance ];

                        break;
                }


                return [ "result" => true, "maintenance" => $maintenance ];
                break;
            case "reject":

                $maintenance->status_id = 6003006; // عدم تایید
                $maintenance->save();
                event( new MaintenanceLogEvent( $maintenance, 6003107 ) );

                return [ "result" => true, "maintenance" => $maintenance ];
                break;

            case "confirm_done":

                switch ( $maintenance->maintenance_type_id ) {
                    case "100": // تغییر تراکم

                        1 / 0;

                        break;
                    case "200": // رفع نقص
                        $machine_fault = MachineFault::find( $maintenance->other_id );

                        if ( ! $machine_fault ) {
                            return [ "result" => false, "error" => "مقدار نقص ماشین معتبر نمی باشد." ];
                        }
                        $list = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"        => 6004003 // در انتظار تایید رفع نقص شده
                        ] )->get();
                        if ( count( $list ) == 0 ) {
                            return [
                                "result" => false,
                                "error"  => " نقص برای ماشین فعال نمی باشد، لطفا با پشتیبانی تماس بگیرید."
                            ];
                        }

                        $list = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"          => 6004003 // در انتظار تایید رفع نقص شده
                        ] )->update( [
                            "active_status_id" => 1210,
                            "status_id"        => 6004004 //رفع نقص شده
                        ] );

                        $maintenance->status_id = 6003003; // خاتمه یافته
                        $maintenance->save();

                        event( new MaintenanceLogEvent( $maintenance, 6003105 ) );

                        return [ "result" => true, "maintenance" => $maintenance ];

                        break;
                }

                break;
            case "reject_done": //عدم تایید انجام نت

                switch ( $maintenance->maintenance_type_id ) {
                    case "100": // تغییر تراکم

                        1 / 0;

                        break;
                    case "200": // رفع نقص
                        $machine_fault = MachineFault::find( $maintenance->other_id );

                        if ( ! $machine_fault ) {
                            return [ "result" => false, "error" => "مقدار نقص ماشین معتبر نمی باشد." ];
                        }
                        $list = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"        => 6004003 // در انتظار تایید رفع نقص شده
                        ] )->get();
                        if ( count( $list ) == 0 ) {
                            return [
                                "result" => false,
                                "error"  => " نقص برای ماشین فعال نمی باشد، لطفا با پشتیبانی تماس بگیرید."
                            ];
                        }

                        $list = CurrentMachineFault::where( [
                            "machine_id"       => $maintenance->machine_id,
                            "machine_fault_id" => $maintenance->other_id,
                            "status_id"          => 6004003 // در انتظار تایید رفع نقص شده
                        ] )->update( [
                            "active_status_id" => 1210,
                            "status_id"        => 6004002 // تایید شده
                        ] );

                        $maintenance->status_id = 6003001; // در انتظار شروع
                        $maintenance->save();
                        event( new MaintenanceLogEvent( $maintenance, 6003106 ) );

                        return [ "result" => true, "maintenance" => $maintenance ];

                        break;
                }


                return [ "result" => true, "maintenance" => $maintenance ];

                break;
        }
    }

    public static function get_density_value_for_maintenance( $allocation ) {

        $value     = [ 0 => "*", 1 => "*", 2 => "*", 3 => "*" ];
        $checklist = [
            220224,  // تراکم پود 1
            220225, // تراکم پود 2
        ];
        // گرفتن تخصیص قبلی
        $before_allocation = Allocation::
        where( "machine_id", $allocation->machine_id )->
        where( "id", "<", $allocation->id )->
        where( "status_id", 5310020 )->
        orderByDesc( "id" )->
        first();

        if ( ! isset( $before_allocation ) ) {
            $value[0] = "---";
            $value[1] = "---";
        } else {
            // Product_change
            $before_machine_allocation = MachineAllocation::
            where( "allocation_id", $before_allocation->id )->
            where( "band_code", 1 )->first();


            $before_value = GoodsKindPropertyValue::
            where( "product_id", $before_machine_allocation->product_id )->
            whereIn( "goods_kind_property_id", $checklist )->orderBy( "goods_kind_property_id" )->
            pluck( "value" )->toArray();
            $value[0]     = isset( $before_value[0] ) ? $before_value[0] : "---";
            $value[1]     = isset( $before_value[1] ) ? $before_value[1] : "---";

        }


        $current_machine_allocation = MachineAllocation::
        where( "allocation_id", $allocation->id )->
        where( "band_code", 1 )->first();

        $current_value = GoodsKindPropertyValue::
        where( "product_id", $current_machine_allocation->product_id )->
        whereIn( "goods_kind_property_id", $checklist )->orderBy( "goods_kind_property_id" )->
        pluck( "value" )->toArray();
        $value[2]      = isset( $current_value[0] ) ? $current_value[0] : "---";
        $value[3]      = isset( $current_value[1] ) ? $current_value[1] : "---";

        return $value;
    }
}
