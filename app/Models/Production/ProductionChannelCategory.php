<?php

namespace App\Models\Production;

use App\Models\LineProduct\GoodsKind;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionChannelCategory extends Model {
    use HasFactory;

    protected $fillable = [ "id", "caption"];
    protected $table="station_operation_categories";

}
