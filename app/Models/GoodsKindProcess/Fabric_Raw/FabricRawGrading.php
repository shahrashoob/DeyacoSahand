<?php

namespace App\Models\GoodsKindProcess\Fabric_Raw;

use App\Models\Form\Packing\PackingFormItem;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Production\ProductionForm;
use App\Models\Production\ProductionFormItem;
use App\Models\Utility\Status;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Generator\RandomGeneratorFactory;

class FabricRawGrading extends Model {
    use HasFactory;
    use Loggable;
    protected $table = "fabric_raw_grading";
    protected $fillable = [
        "section_shift_work_number",
        "section_degree_number",
        "production_id",
        "production_form_id",
        "production_form_item_id",
        "product_id",
        "degree_id",
        "shift_work_id",
        "carrier_id",
        "lot_number_id",
        "band_code",
        "start_point",
        "end_point",
        "amount",
        "sub_amount",
        "form_id",
        "form_item_id",
        "status_id",
    ];

    public function production() {
        return $this->belongsTo( Production::class );
    }

    public function production_form() {
        return $this->belongsTo( ProductionForm::class );
    }

    public function production_form_item() {
        return $this->belongsTo( ProductionFormItem::class );
    }

    public function packing_form_item() {
        return $this->belongsTo( PackingFormItem::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function lot_number() {
        return $this->belongsTo( LotNumber::class );
    }

    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public function status() {
        return $this->belongsTo( Status::class );
    }

    public function getCode() {

        if ( $this->code != ""  ) {
            return $this->code;
        }

        $this->code = $this->production_form->getCode() . "/" . $this->band_code . "/" . $this->section_degree_number;
        $this->save();

        return $this->code;
    }

    public static function updateCode(){
        foreach (FabricRawGrading::where("code","")->get() as $item){
            $item->getCode();
        }
    }

    public static function AddNewItem( ProductionFormItem $production_form_item, $latest_record ,$lot_number_id) {
        $grading = FabricRawGrading::create( [
            "section_degree_number"   => isset( $latest_record ) ? $latest_record->section_degree_number + 1 : 1,
            "production_id"           => $production_form_item->production_id,
            "production_form_id"      => $production_form_item->production_form_id,
            "production_form_item_id" => $production_form_item->id,
            "product_id"              => $production_form_item->product_id,
            "lot_number_id"           => $lot_number_id,
            "band_code"               => $production_form_item->band_code,
            "start_point"             => isset( $latest_record ) ? $latest_record->end_point : 0,
            "status_id"               => 7006001,
        ] );

        return $grading;
    }
}
