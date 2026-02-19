<?php

namespace App\Models\Warehouse\Pallet;

use App\Models\Form\Packing\PackingForm;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PalletItem extends Model
{
    use HasFactory;

    protected $table = "pallet_items";
    protected $fillable = ["pallet_id", "packing_form_id"];

    public function packing_form(){
        return $this->belongsTo(PackingForm::class);
    }
    public function pallet(){
        return $this->belongsTo(Pallet::class);
    }

}
