<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SeederDatabase extends Model
{
    use HasFactory;

    protected $table = "seeders";
    protected $fillable = ["seeder", "version"];
}
