<?php

namespace App\Models\LineProduct\Product\ProductRequest;

use App\Models\Form\Form;
use App\Models\Form\FormItem;
use App\Models\GoodsKindProcess\Warps\RequestForm\WarpsRequestFormPackingType;
use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Degree;
use App\Models\LineProduct\LotNumber;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use App\Models\Utility\Transport\TransportPackingForm;
use App\Models\Warehouse\Warehouse;
use App\Models\Warehouse\WarehouseProduct;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductRequestFormItem extends Model {
    use HasFactory;

    protected $table = "product_request_form_item";
    protected $fillable = [
        "product_request_form_id",
        "production_id",
        "product_id",
        "warehouse_product_id",
        "band_code",
        "current_machine_input_output_band_id",
        "input_line_code",
        "amount_request",
        "amount_remaining",
        "order_list_id",
        "degree_id",
        "can_deliver_material_with_different_lot",
        "min_number_of_packing_forms",
        "max_number_of_packing_forms"
    ];

    public function product() {
        return $this->belongsTo( Product::class );
    }
    public function degree() {
        return $this->belongsTo( Degree::class );
    }

    public function production() {
        return $this->belongsTo( Production::class );
    }

    public function product_request_form() {
        return $this->belongsTo( ProductRequestForm::class );
    }

    public function product_request_form_packing_types() {
        return $this->hasMany( ProductRequestFormPackingType::class, "product_request_form_item_id" );
    }

    public function getDeliveringAmount() {
        $form_ids   = $this->product_request_form->forms()->pluck( "form_id" )->toArray();
        $form_ids[] = - 1;

        // در انتظار تایید انبار
        $form_ids   = Form::whereIn( "id", $form_ids )->whereIn( "status_id", [500000410,500000500] )->pluck( "id" )->toArray();
        $form_ids[] = - 1;

       // return FormItem::whereIn( "form_id", $form_ids )->first();
        return FormItem::whereIn( "form_id", $form_ids )->where( "product_id", $this->product_id )->sum( "amount" );

    }

}
