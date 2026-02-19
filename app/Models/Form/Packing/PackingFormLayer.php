<?php

namespace App\Models\Form\Packing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingFormLayer extends Model
{
    use HasFactory;
    protected $table="packing_form_layer";
    protected $fillable=["packing_form_id","packing_form_item_id","carrier_id","band_code","layer_code"];


}
