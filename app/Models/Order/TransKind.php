<?php

namespace App\Models\Order;

use App\Models\Warehouse\EntryType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransKind extends Model
{
    use HasFactory;

    public function entry_type()
    {
        return $this->belongsTo(EntryType::class);
    }
}
