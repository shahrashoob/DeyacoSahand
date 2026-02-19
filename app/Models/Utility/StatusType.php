<?php

namespace App\Models\Utility;

use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\MachineStatus;
use App\Models\LineProduct\Station;
use App\Models\Order\Permision\OrderPermissionType;
use App\Models\Production\ProductionFormStatus;
use App\Models\Production\ProductionWaitingStatus;
use App\Models\Utility\Module\Module;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class StatusType extends Model {
    use HasFactory;
    public $timestamps = false;

}
