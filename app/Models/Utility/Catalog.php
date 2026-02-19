<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    use HasFactory;
protected $table="catalogs_info_tables";
    protected $fillable = ["fullname",
        "company", "mobile"];
}
