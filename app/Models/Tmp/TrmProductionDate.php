<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TrmProductionDate extends Model
{
    use HasFactory;
    protected $table="tem_production_date";
    public $timestamps=false;
}
