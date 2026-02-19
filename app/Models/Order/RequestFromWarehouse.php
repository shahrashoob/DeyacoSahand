<?php

namespace App\Models\Order;

use App\Models\Utility\Message;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use Illuminate\Support\Facades\Auth;

class RequestFromWarehouse extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "request_from_warehouse";
    protected $fillable = [
        "allocation_id",
        "order_id",
        "order_list_id",
        "product_id",
        "customer_id",
        "production_card_id",
        "perchase_order_id",
        "call_id",
        "material_id",
        "amount",
        "sub_amount",
        "supply_type_id",
        "status_id",

    ];

    public function material() {
        return $this->belongsTo( Product::class, "material_id", "id" );
    }


    public function production() {
        return $this->belongsTo( Production::class, "production_card_id", "id" );
    }


    public function log( $message = "", $status_id = null ) {

        $msg = null;
        if ( $message != "" ) {
            $msg = Message::create(
                [
                    "text"            => $message,
                    "other_id"        => $this->id,
                    "message_type_id" => 120
                ]
            );
        }


        return RequestFromWarehouseLog::create( [
            "rfw_id"     => $this->id,
            "status_id"  => $status_id ? $status_id : $this->status->id,
            "message_id" => $msg->id ?? 0,
            "user_id"    => Auth::user()->id
        ] );
    }


}
