<?php

namespace App\Models\Utility\Algorithm\RequestRawMaterialAlgorithm;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\GoodsKind;
use App\Models\LineProduct\Machine\CurrentMachineInput;
use App\Models\LineProduct\Machine\Machine;
use App\Models\LineProduct\Machine\MachineAllocation;
use App\Models\LineProduct\Machine\MachineLog;
use App\Models\LineProduct\Machine\MachineModuleType;
use App\Models\LineProduct\Machine\MachineTypeInputBandGoodsKind;
use App\Models\LineProduct\Product;
use App\Models\Production\ProductionFormItem;
use App\Models\HR\Shift\Shift;
use App\Models\Utility\Script\Script;
use App\Models\Warehouse\Warehouse;
use Carbon\Carbon;

class Algorithm2WithMinMaxCapacity extends Controller {

    // داخل این الگوریتم در کلاس زیر پیاده سازی شده است و بعد باید برای یک دست شدن الگوریتم ها همه آنها را یکی کنیم.
    public function empty()
    {
        \App\Models\LineProduct\Machine\RequestRawMaterialAlgorithm\Algorithm2WithMinMaxCapacity::RequestForMachine();
    }
}
