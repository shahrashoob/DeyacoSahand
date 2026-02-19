<?php

namespace App\Models\Tmp;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrmProductionCard extends Model
{
    use HasFactory;
    protected $table="tmp_production_card";
    public $timestamps=false;
}
