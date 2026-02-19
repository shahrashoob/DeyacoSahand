<?php

namespace App\Models\Production;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionReplace extends Model
{
    use HasFactory;
    protected  $table="production_replaces";
    protected $fillable=["production_id","production_replace_id","production_log_id"];
}
