<?php

namespace App\Models\Warehouse\WarehouseHandling;

use App\Models\Form\Form;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseHandlingForm extends Model
{
    use HasFactory;

    protected $table = "warehouse_handling_form";
    protected $fillable = ["warehouse_handling_id", "input_form_id", "output_form_id"];

    public function warehouse_handling()
    {
        return $this->belongsTo(WarehouseHandling::class,"warehouse_handling_id");
    }
    public function input_form()
    {
        return $this->belongsTo(Form::class,"input_form_id");
    }
    public function output_form()
    {
        return $this->belongsTo(Form::class,"output_form_id");
    }
}
