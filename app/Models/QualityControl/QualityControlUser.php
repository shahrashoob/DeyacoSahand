<?php

namespace App\Models\QualityControl;

use App\Models\Accounting\CostCenter;
use App\Models\HR\Company\Company;
use App\Models\File\File;
use App\Models\HR\Personal\PersonalType;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\SupplyType;
use App\Models\Post\Post;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class QualityControlUser extends Model
{
    use HasFactory;

    protected $table = "quality_control_users";
    protected $fillable = [
        'packing_form_id',
        'user_id',
        'is_supervisor'
    ];
}