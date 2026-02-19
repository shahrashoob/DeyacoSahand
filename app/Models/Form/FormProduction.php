<?php

namespace App\Models\Form;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormProduction extends Model
{
    use HasFactory;
    protected $table="form_production";
    protected $fillable=["form_id","production_id"];
}
