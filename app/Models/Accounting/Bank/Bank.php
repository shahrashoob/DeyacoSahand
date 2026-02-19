<?php

namespace App\Models\Accounting\Bank;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bank extends Model
{
    use HasFactory;

    protected $fillable = [
        "is_bank_allowed_to_choose",
        "caption"
    ];
    protected $table = 'banks';
}
