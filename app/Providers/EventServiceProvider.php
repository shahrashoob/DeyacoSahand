<?php

namespace App\Providers;

use App\Events\Contractor\ContractorLogEvent;
use App\Events\Fabric_Raw\DesingFormEvent;
use App\Events\Fabric_Raw\FabricRawDesignFormLogEvent;
use App\Events\Fabric_Raw\ProductionFromAmountUpdateEvent;
use App\Events\Form\PackingLogEvent;
use App\Events\HR\EmploymentLogEvent;
use App\Events\HR\EvaluationFormLogEvent;
use App\Events\HR\LeaveLogEvent;
use App\Events\HR\OfficeAutomationLogEvent;
use App\Events\Machine\MachineAllocationEvent;
use App\Events\Machine\MachineLogEvent;
use App\Events\Machine\MaintenanceLogEvent;
use App\Events\Order\OrderLogEvent;
use App\Events\Product\ProductCreationProcessLogEvent;
use App\Events\Product\ProductRequestFormLogEvent;
use App\Events\Product\RejectProductLogEvent;
use App\Events\ProductionCard\ProductionCardLogEvent;
use App\Events\ProductionForm\ProductionFormLogEvent;
use App\Events\TestEvent;
use App\Events\Utility\FinancialSoftwareTransKindFormLogEvent;
use App\Events\Utility\SpecialLicenseEvent;
use App\Events\Utility\TransportLogEvent;
use App\Events\Warehouse\Form\FormLogEvent;
use App\Events\Warehouse\PutInWarehouseEvent;
use App\Events\Warehouse\WarehouseHandlingEvent;
use App\Events\Warps\WarpsAvailableEvent;
use App\Events\Warps\WarpsRequestFormLogEvent;
use App\Listeners\Contractor\ContractorLogListener;
use App\Listeners\Fabric_Raw\DesingFormListener;
use App\Listeners\Fabric_Raw\FabricRawDesignFormLogListener;
use App\Listeners\Fabric_Raw\ProductionFromAmountUpdateListener;
use App\Listeners\Form\PackingLogListener;
use App\Listeners\HR\EmploymentLogListener;
use App\Listeners\HR\EvaluationFormLogListener;
use App\Listeners\HR\LeaveLogListener;
use App\Listeners\HR\OfficeAutomationLogListener;
use App\Listeners\Machine\MachineAllocationListener;
use App\Listeners\Machine\MachineLogListener;
use App\Listeners\Machine\MaintenanceLogListener;
use App\Listeners\Order\OrderLogListener;
use App\Listeners\Product\ProductCreationProcessLogListener;
use App\Listeners\Product\ProductRequestFormLogListener;
use App\Listeners\Product\RejectProductLogListener;
use App\Listeners\ProductionCard\ProductionCardLogListener;
use App\Listeners\ProductionForm\ProductionFormLogListener;
use App\Listeners\TestListener;
use App\Listeners\Utility\FinancialSoftwareTransKindFormLogListener;
use App\Listeners\Utility\SpecialLicenseListener;
use App\Listeners\Utility\TransportLogListener;
use App\Listeners\Warehouse\Form\FormLogListener;
use App\Listeners\Warehouse\PutInWarehouseListener;
use App\Listeners\Warehouse\WarehouseHandlingListener;
use App\Listeners\Warps\WarpsAvailableListener;
use App\Listeners\Warps\WarpsRequestFormLogListener;
use App\Models\LineProduct\Product\RejectProduct\RejectProductLog;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider {
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class      => [
            SendEmailVerificationNotification::class,
        ],
        TestEvent::class       => [
            TestListener::class
        ],

        ProductionCardLogEvent::class=>[
            ProductionCardLogListener::class
        ],
        MachineLogEvent::class=>[
            MachineLogListener::class
        ],
        MachineAllocationEvent::class=>[
            MachineAllocationListener::class
        ],
        FormLogEvent::class=>[
            FormLogListener::class
        ],
        PutInWarehouseEvent::class=>[
            PutInWarehouseListener::class
        ],
        WarehouseHandlingEvent::class=>[
            WarehouseHandlingListener::class
        ],
        ProductionFormLogEvent::class=>[
            ProductionFormLogListener::class
        ],
        PackingLogEvent::class=>[
            PackingLogListener::class
        ],
        MaintenanceLogEvent::class=>[
            MaintenanceLogListener::class
        ],

        // Fabric Raw
        FabricRawDesignFormLogEvent::class=>[
            FabricRawDesignFormLogListener::class
        ],
        ProductionFromAmountUpdateEvent::class=>[
            ProductionFromAmountUpdateListener::class
        ],

        //
        ProductRequestFormLogEvent::class=>[
            ProductRequestFormLogListener::class
        ],
        RejectProductLogEvent::class=>[
            RejectProductLogListener::class
        ],
        ProductCreationProcessLogEvent::class=>[
            ProductCreationProcessLogListener::class
        ],
        ContractorLogEvent::class=>[
            ContractorLogListener::class
        ],
        // Warps
        WarpsRequestFormLogEvent::class=>[
            WarpsRequestFormLogListener::class
        ],
        WarpsAvailableEvent::class=>[
            WarpsAvailableListener::class
        ],
        LeaveLogEvent::class=>[
            LeaveLogListener::class
        ],
        EvaluationFormLogEvent::class=>[
            EvaluationFormLogListener::class
        ],
        EmploymentLogEvent::class=>[
            EmploymentLogListener::class
        ],
        OfficeAutomationLogEvent::class=>[
            OfficeAutomationLogListener::class
        ],
        OrderLogEvent::class=>[
            OrderLogListener::class
        ],

        // Transport
        TransportLogEvent::class=>[
            TransportLogListener::class
        ],
        // Special License
        SpecialLicenseEvent::class=>[
            SpecialLicenseListener::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot() {
        //
    }
}
