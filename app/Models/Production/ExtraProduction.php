<?php

namespace App\Models\Production;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtraProduction extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="extra_prodaction";
    protected $fillable=["product_id"];
}
