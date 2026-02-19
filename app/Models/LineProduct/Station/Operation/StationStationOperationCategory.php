<?php

namespace App\Models\LineProduct\Station\Operation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StationStationOperationCategory extends Model
{
    use HasFactory;

    protected $table = "station_station_operation_category";
    protected $fillable = ["station_id", "station_operation_category_id"];

    public function station_operation_category()
    {
        return $this->belongsTo(StationOperationCategory::class);
    }
}