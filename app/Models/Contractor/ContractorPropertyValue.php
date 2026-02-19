<?php

namespace App\Models\Contractor;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorPropertyValue extends Model {
    use HasFactory;
    use Loggable;

    protected $table = "contractor_property_value";
    protected $fillable = [ "contractor_property_id", "contractor_id" ];
}
