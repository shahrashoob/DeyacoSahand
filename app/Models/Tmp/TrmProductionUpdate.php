<?php

namespace App\Models\Tmp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrmProductionUpdate extends Model
{
    use HasFactory;
    protected $table="tmp_production_card_update";
    public $timestamps=false;
}
