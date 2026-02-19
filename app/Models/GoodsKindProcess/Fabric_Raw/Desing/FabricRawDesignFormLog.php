<?php

namespace App\Models\GoodsKindProcess\Fabric_Raw\Desing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricRawDesignFormLog extends Model {
    use HasFactory;
    protected $fillable = [
        "fabric_raw_design_form_id",
        "design_available",
        "it_has_pinning",
        "warps_is_in_warehouse",
        "need_to_convert",
        "status_id",
        "user_id",
        "message+id"
    ];
}
