<?php

namespace App\Models\Warehouse;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseFinancialSetting extends Model
{
    use HasFactory;
    use Loggable;
    protected $table="warehouse_financial_setting";

}
