<?php

namespace App\Models\LineProduct;

use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorOperation;
use App\Models\Customer\Customer;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\LineProduct\Product\ProductRoute;
use App\Models\LineProduct\Station\Operation\StationSubOperation;
use App\Models\Production\ProductionChannelType;
use App\Models\Production\ProductionMethod;
use App\Models\Supplier\Supplier;
use App\Models\Utility\Status;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LineProductStation extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "line_product_station";

    protected $fillable = [
        "product_id",
        "line_id",
        "practical_capacity_of_production",
        "min_of_production",
        "max_of_production",
        "efficiency",
        "setup_time",
        "station_id",
        "machine_type_id",
        "status_id",
        "ic",
        "priority_number",
        "machine_type_id",
        "station_operation_id",
        "product_route_id",
        "contractor_id",
        "contractor_operation_id",
        "delivery_time",
        "receiving_time",
        "is_need_for_quality_control",

        "batch",
        "batch_error_percentage",
        "material_id_dependent_to_batch",
        "material_unit_type_id_dependent_to_batch",
        "material_packing_type_id_dependent_to_batch",

        "extra_production",
        "percent_of_extra_production",
        "production_channel_type_id",

        "station_sub_operation_id",
        "practical_capacity_of_production",


        "setup_time_for_co_channel",
        "setup_time_for_non_co_channel",
        "setup_time_for_sub_operation",
        "setup_time_for_final_setting",

        "supplier_id",
        "purchasing_capacity",

        "product_code_in_contractor_system", // کد کالا در سامانه پیمانکار (یا مشتری)
        "service_code_in_contractor_system", // کد خدمت در سامانه پیمانکار (یا مشتری)

        "product_caption_in_supplier_system", // نام کالا در سامانه تامین کننده
        "product_code_in_supplier_system", // کد کالا در سامانه تامین کننده

        "is_need_start_setup",
        "is_need_start_of_operation",
        "is_need_end_of_operation",
        "is_need_for_quality_control",
        "is_ability_to_choose_next_station",
        "is_need_final_setting",
        "is_need_allocation_at_first",

        "has_control_sample",
        "line_product_start_status_id",
        "delay_in_the_start_minute",

        "customer_id",
        "applicant_warehouse_id",

        "production_method_id"
    ];

    public static function exist($id, $product_id, $product_route_id, $supply_type_id, $line_id, $station_id, $machine_type_id, $station_operation_id, $contractor_operation_id = null, $station_sub_operation_id = -1, $supplier_id = null, $customer_id = null)
    {

        if ($supply_type_id == 2) {
            //تامین کنندگان
            if ($id) {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "supplier_id" => $supplier_id
                ])->where("id", "!=", $id)->first();
            } else {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "supplier_id" => $supplier_id
                ])->first();
            }
        } elseif ($supply_type_id == 3) {
            //پیمانکاری
            if ($id) {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "contractor_operation_id" => $contractor_operation_id
                ])->where("id", "!=", $id)->first();
            } else {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "contractor_operation_id" => $contractor_operation_id
                ])->first();
            }
        } elseif ($supply_type_id == 4) {
            // مشتری
            if ($id) {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "customer_id" => $customer_id
                ])->where("id", "!=", $id)->first();
            } else {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "customer_id" => $customer_id
                ])->first();
            }
        } else {
            // اگر به اشتباه این متغیر را ارسال نکرده بودیم، خطا بدهد.
            if ($station_sub_operation_id == -1) {
                return false;
            }
            if ($id) {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "product_route_id" => $product_route_id,
                    "line_id" => $line_id,
                    "station_id" => $station_id,
                    "machine_type_id" => $machine_type_id,
                    "station_operation_id" => $station_operation_id,
                    "station_sub_operation_id" => $station_sub_operation_id
                ])->where("id", "!=", $id)->first();
            } else {
                return LineProductStation::where([
                    "product_id" => $product_id,
                    "product_route_id" => $product_route_id,
                    "line_id" => $line_id,
                    "station_id" => $station_id,
                    "machine_type_id" => $machine_type_id,
                    "station_operation_id" => $station_operation_id,
                    "station_sub_operation_id" => $station_sub_operation_id
                ])->first();
            }
        }
    }

    public function route()
    {
        return $this->belongsTo(ProductRoute::class, "product_route_id");
    }

    public function line()
    {
        return $this->belongsTo(Line::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function production_method()
    {
        return $this->belongsTo(ProductionMethod::class);
    }

    public function applicant_warehouse()
    {
        return $this->belongsTo(Warehouse::class,"applicant_warehouse_id");
    }

    public function material_dependent_to_batch()
    {
        return $this->belongsTo(Product::class, "material_id_dependent_to_batch");
    }

    public function material_unit_type_dependent_to_batch()
    {
        return $this->belongsTo(Unit\UnitType::class, "material_unit_type_id_dependent_to_batch");
    }

    public function material_packing_type_dependent_to_batch()
    {
        return $this->belongsTo(PackingType::class
            , "material_packing_type_id_dependent_to_batch");
    }

    public function get_unit_type_dependent_to_batch_caption(Product $product)
    {
        switch ($this->material_unit_type_id_dependent_to_batch) {
            case 1:
                return $product->unit->caption;
                break;
            case 2:
                return $product->sub_unit->caption;
            case 3:
                return $product->sub_unit2->caption;
            case 4:
                return "عدد";
        }
        
    }

    public function station()
    {
        return $this->belongsTo(Station::class);
    }

    public function machine_type()
    {
        return $this->belongsTo(MachineType::class);
    }

    public function station_operation()
    {
        return $this->belongsTo(StationOperation::class);
    }

    public function station_sub_operation()
    {
        return $this->belongsTo(StationSubOperation::class);
    }

    public function production_channel_type()
    {
        return $this->belongsTo(ProductionChannelType::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }


    public function line_product_start_status()
    {
        return $this->belongsTo(Status::class,"line_product_start_status_id");
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function contractor_operation()
    {
        return $this->belongsTo(ContractorOperation::class);
    }
}
