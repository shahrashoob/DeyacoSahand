<?php

namespace App\Models\GoodsKindProcess\Fabric_Raw\Desing;

use App\Models\LineProduct\Product;
use App\Models\Production\Production;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FabricRawDesignFormProduction extends Model
{
    use HasFactory;

    use Loggable;
    protected $table="fabric_raw_design_form_production";
    protected $fillable=["fabric_raw_design_form_id","production_id","band_code"];



    public function production(){
        return $this->belongsTo(Production::class);
    }
}
