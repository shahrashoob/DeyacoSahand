<?php

namespace App\Models\Report\R1003;

use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report1003_remove extends Model {
    use HasFactory;

    protected $table = "report_1003";
    protected $fillable = [ "user_id", "data" ];



}
