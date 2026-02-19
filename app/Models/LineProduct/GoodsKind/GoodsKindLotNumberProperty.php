<?php

namespace App\Models\LineProduct\GoodsKind;

use App\Models\Utility\FieldType;
use App\Models\Utility\SpecialUnit;
use App\Models\Utility\Status;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GoodsKindLotNumberProperty extends Model
{

    public function lot_number_property()
    {
        return $this->belongsTo(LotNumberProperty::class);
    }
}