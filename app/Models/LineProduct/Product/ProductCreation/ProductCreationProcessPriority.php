<?php

namespace App\Models\LineProduct\Product\ProductCreation;

use App\Models\Utility\Menu\Button;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCreationProcessPriority extends Model
{
    use HasFactory;

    protected $table = "product_creation_process_priority";
    protected $fillable = ["goods_kind_id", "confirm_next_button_id", "next_status_id", "before_status_id",'description','post_id'];

    public function button()
    {
        return $this->belongsTo(Button::class);
    }
}
