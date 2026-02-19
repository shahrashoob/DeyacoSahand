<?php

namespace App\Models\HR\Shift;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShiftWorkGroup extends Model
{
    use HasFactory;
    protected $fillable=["shift_id","shift_work_id","shift_work_group_type_id"];
}
