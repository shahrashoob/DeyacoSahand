<?php

namespace App\Models\Form\Packing;

use App\Models\Contractor\Contractor;
use App\Models\Customer\Customer;
use App\Models\Form\Form;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Product;
use App\Models\Warehouse\Warehouse;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class   PackingFormItemImportantStatus extends Model {
    use HasFactory;

    protected $table = "packing_form_item_important_status";
    protected $fillable = [
        "packing_form_id",
        "packing_form_item_id",
        "product_id",
        "goods_kind_id",
        "exit_form_id",
        "amount",


        "packing_created_at",
        "production_form_item_id",
        "completion_and_delivery_to_warehouse_at",
        "confirm_warehouse_at",
        "create_transport_item_at",

        "allocation_id",

        "production_id",
        "order_id",
        "order_code",
        "order_series",
        "parent_production_id",
        "production_serial",
        "parent_production_serial",
        "event_7007009_at"

    ];

    public function packing_form_item() {
        return $this->belongsTo( PackingFormItem::class );
    }

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );
    }

    public function goods_kind() {
        return $this->belongsTo( GoodsKind::class );
    }

    public function product() {
        return $this->belongsTo( Product::class );
    }

    public function exit_form() {
        return $this->belongsTo( Form::class, "exit_form_id" );
    }


}

