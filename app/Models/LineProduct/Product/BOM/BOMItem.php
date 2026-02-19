<?php

namespace App\Models\LineProduct\Product\BOM;


use App\Models\Contractor\Contractor;
use App\Models\Contractor\ContractorOperation;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product;
use App\Models\LineProduct\Station;
use App\Models\LineProduct\StationOperation;
use App\Models\Production\Production;
use App\Models\Utility\Status;
use App\Models\Utility\Unit;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Mpdf\Tag\B;

class BOMItem extends Model {

    use HasFactory;

    protected $table = "bill_of_material_item";
    protected $fillable = [
        "bill_of_material_id",
        "product_id",
        "material_id",
        "amount",
        "version",
        "production_status_id",
        "warehouse_type_id",
        "warehouse_id",
        "station_id",
        "number",
        "percent_of_use",
        "station_operation_id",
        "station_sub_operation_id",
        "input_line_code",
        "delivery_unit_id",

        "contractor_id",
        "contractor_operation_id",

        "consumption_correction_factor",
        "consumption_correction_factor_prediction",
        "waste_prediction",

        "productive_consume_warehouse_type_id",
        "productive_consume_warehouse_id",
        "sampling_consume_warehouse_type_id",
        "sampling_consume_warehouse_id",

        "consumption_percent_of_production_channel",
        "priority_number_in_replace",

        "bill_of_material_dependency_type_id",
        "dependent_on_material_id",
        "dependent_on_main_unit_type_id",
        "dependent_on_material_unit_type_id",

        "is_structure_product", // آیا این ردیف BOM کالای ساختاری می باشد؟

        "bill_of_material_entering_type_id", // روش ورود مواد اولیه به ماشین
    ];

    public function bom() {
        return $this->belongsTo( BOM::class, "bill_of_material_id" );
    }

    public function material() {
        return $this->belongsTo( Product::class, "material_id", "id" );
    }
    public function bill_of_material_entering_type() {
        return $this->belongsTo( BOMItemEnteringType::class, "bill_of_material_entering_type_id", "id" );
    }

    public function production_status() {
        return $this->belongsTo( Status::class, "production_status_id", "id" );
    }

    public function warehouse_type() {
        return $this->belongsTo( WarehouseType::class );
    }
    public function warehouse() {
        return $this->belongsTo( Warehouse::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }
    /*
     * کالای BOM به کدام یک از مواد اولیه وابسته است.
     */
    public function dependent_on_material() {
        return $this->belongsTo( Product::class,"dependent_on_material_id" );
    }
    public function dependent_on_main_unit_type() {
        return $this->belongsTo( Unit\UnitType::class,"dependent_on_main_unit_type_id" );
    }
    public function get_dependent_on_main_unit_type() {
        if($this->dependent_on_material_id == null){
            $product=$this->product;
        }
        else{
            $product=$this->dependent_on_material;
        }
        switch ($this->dependent_on_main_unit_type_id){
            case 1:
                return $product->unit->caption??"<span class='text-danger'>نامعتبر</span>";
                break;
            case 2:
                return $product->sub_unit->caption??"<span class='text-danger'>نامعتبر</span>";
                break;
            case 3:
                return $product->sub_unit2->caption??"<span class='text-danger'>نامعتبر</span>";
                break;
        }

    }
    public function dependent_on_material_unit_type() {
        return $this->belongsTo( Unit\UnitType::class,"dependent_on_material_unit_type_id" );
    }
    public function get_dependent_on_material_unit_type() {

        switch ($this->dependent_on_material_unit_type_id){
            case 1:
                return $this->material->unit->caption??"";
                break;
            case 2:
                return $this->material->sub_unit->caption??"";
                break;
            case 3:
                return $this->material->sub_unit2->caption??"";
                break;
        }
    }

    public function productive_consume_warehouse_type() {
        return $this->belongsTo( WarehouseType::class, "productive_consume_warehouse_type_id" );
    }

    public function productive_consume_warehouse() {
        return $this->belongsTo( Warehouse::class, "productive_consume_warehouse_id" );
    }

    public function sampling_consume_warehouse_type() {
        return $this->belongsTo( WarehouseType::class, "sampling_consume_warehouse_type_id" );
    }

    public function sampling_consume_warehouse() {
        return $this->belongsTo( Warehouse::class, "sampling_consume_warehouse_id" );
    }

    public function station() {
        return $this->belongsTo( Station::class );
    }

    public function station_operation() {
        return $this->belongsTo( StationOperation::class );
    }

    public function station_sub_operation() {
        return $this->belongsTo( Station\Operation\StationSubOperation::class );
    }

    public function contractor() {
        return $this->belongsTo( Contractor::class );
    }

    public function contractor_operation() {
        return $this->belongsTo( ContractorOperation::class );
    }

    public function degrees() {
        return $this->hasMany( BOMDegree::class, "bill_of_material_item_id" );
    }

    public function replaces() {
        return $this->hasMany( BOMReplace::class, "bill_of_material_item_id" );
    }

    public function delivery_unit() {
        return $this->belongsTo( Unit::class, "delivery_unit_id" );
    }

    public function has_material_goods_kind_fault() {
        return $this->material->goods_kind->product_fault()->count();
    }

    public function bom_fault_illegals() {
        return $this->hasMany( BOMFaultIllegal::class, "bill_of_material_item_id" );
    }

    public static function exist( $id, $bom_id, $product_id, $supply_type_id, $material_id, $station_id, $station_operation_id, $contractor_operation_id ) {

        switch ( $supply_type_id ) {
            case 1:
                if ( $id ) {
                    return BOMItem::where( [
                        "bill_of_material_id"  => $bom_id,
                        "product_id"           => $product_id,
                        "material_id"          => $material_id,
                        "station_id"           => $station_id,
                        "station_operation_id" => $station_operation_id
                    ] )->where( "id", "!=", $id )->first();
                } else {
                    return BOMItem::where( [
                        "bill_of_material_id"  => $bom_id,
                        "product_id"           => $product_id,
                        "material_id"          => $material_id,
                        "station_id"           => $station_id,
                        "station_operation_id" => $station_operation_id
                    ] )->first();
                }
                break;

            case 3:
                if ( $id ) {
                    return BOMItem::where( [
                        "bill_of_material_id"     => $bom_id,
                        "product_id"              => $product_id,
                        "material_id"             => $material_id,
                        "contractor_operation_id" => $contractor_operation_id
                    ] )->where( "id", "!=", $id )->first();
                } else {
                    return BOMItem::where( [
                        "bill_of_material_id"     => $bom_id,
                        "product_id"              => $product_id,
                        "material_id"             => $material_id,
                        "contractor_operation_id" => $contractor_operation_id
                    ] )->first();
                }
                break;
        }


    }

    public static function get_percent_of_use( BOMItem $BOM_item ) {
        // ممکن است یک کالا در bom مثل نخ تکراری باشد، ولی با درصد استفاده های مختلف که حداکثر 100 هستند.
        return BOMItem::where( [
            "bill_of_material_id"  => $BOM_item->bill_of_material_id,
            "product_id"           => $BOM_item->product_id,
            "material_id"          => $BOM_item->material_id,
            "station_id"           => $BOM_item->station_id,
            "station_operation_id" => $BOM_item->station_operation_id
        ] )->sum( "percent_of_use" );

    }

    /**
     * @param \App\Models\LineProduct\Product\BOM\BOMItem $bom_item
     * @param \App\Models\Production\Production           $production
     * @param \App\Models\LineProduct\Machine\Machine     $machine
     * به دست آوردن انبار مصرف کالا از روی BOM با توجه به نوع کارت تولید
     *
     * @return mixed|void
     */
    public static function getConsumeWarehouseId( BOMItem $bom_item, Production $production, Machine $machine ) {

        switch ( $production->production_type_id ) {
            case 1: // کارت تولیدی
                if ( $bom_item->productive_consume_warehouse_type_id == 2 ) {
                    return $machine->warehouse_id;
                } else {
                    return $bom_item->productive_consume_warehouse_id;
                }
                break;

            case 2: // کارت نمونه گیری
                if ( $bom_item->sampling_consume_warehouse_type_id == 2 ) {
                    return $machine->warehouse_id;
                } else {
                    return $bom_item->sampling_consume_warehouse_id;
                }
                break;

            default:
                1 / 0;
                break;
        }


    }


}
