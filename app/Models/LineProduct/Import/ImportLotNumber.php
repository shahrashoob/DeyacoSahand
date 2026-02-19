<?php

namespace App\Models\LineProduct\Import;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportLotNumber extends Model
{
    use HasFactory;
    protected $table="new_lot_numbers";
}
