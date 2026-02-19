<?php

namespace App\Models\Form\Packing;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PackingFormSubPacking extends Model {
    use HasFactory;

    protected $table = "packing_form_sub_packing";
    protected $fillable = [ "packing_form_id", "parent_packing_form_id" ];

    public function packing_form() {
        return $this->belongsTo( PackingForm::class );

    }

    public function parent() {
        return $this->belongsTo( PackingForm::class, "parent_packing_form_id" );
    }
}
