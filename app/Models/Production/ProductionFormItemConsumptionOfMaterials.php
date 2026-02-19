<?php
//
//namespace App\Models\Production;
//
//use App\Models\Form\FormItem;
//use App\Models\Form\Packing\PackingFormItem;
//use App\Models\GoodsKindProcess\Fabric_Raw\FabricRaw;
//use App\Models\GoodsKindProcess\Fabric_Raw\FabricRawGrading;
//use App\Models\LineProduct\GoodsKind;
//use App\Models\LineProduct\LotNumber;
//use App\Models\LineProduct\Machine\Allocation;
//use App\Models\LineProduct\Machine\MachineLog;
//use App\Models\LineProduct\Product;
//use Haruncpi\LaravelUserActivity\Traits\Loggable;
//use Illuminate\Database\Eloquent\Factories\HasFactory;
//use Illuminate\Database\Eloquent\Model;
//
//class ProductionFormItemConsumptionOfMaterials extends Model {
//    use HasFactory;
//    use Loggable;
//
//    protected $table = "production_form_item";
//    protected $fillable = [
//        "production_form_id",
//        "production_form_item_id",
//        "allocation_id",
//        "product_id",
//        "material_id",
//        "forecast_amount"
//    ];
//
//
//    public function production_form_item() {
//        return $this->hasMany( ProductionFormItem::class );
//    }
//
//    public function production_form() {
//        return $this->hasMany( ProductionForm::class );
//    }
//
//    public function allocation() {
//        return $this->belongsTo( Allocation::class );
//    }
//
//    public function product() {
//        return $this->belongsTo( ProductionForm::class );
//    }
//
//    public function material() {
//        return $this->belongsTo( Product::class, "material_id" );
//    }
//
//}
