<?php

namespace App\Models\LineProduct\Machine\Allocation;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AllocationImportantStatus extends Model {
    use HasFactory;

    protected $table = "allocation_important_status";
    protected $fillable = [
        "allocation_id",
        "allocation_created_at",
        "allocation_coordinating_for_delivery_at",
        "allocation_raw_delivery_at"
    ];
}
