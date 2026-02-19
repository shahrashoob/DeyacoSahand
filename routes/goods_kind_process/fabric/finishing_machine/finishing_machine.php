<?php

use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine\ConfirmTestingController;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\ProductionCard as ProductionCard;
use App\Http\Controllers\GoodsKindProcess\Fabric\FinishingMachine\Machine as Machine;


Route::name("machine_allocation.")->prefix("/machine_allocation")->group(function () {
    Route::get("index/{production}/{machine_type}", [
        ProductionCard\MachineAllocationController::class,
        "index"
    ])->name("index");

    Route::match(['get', 'post'], "select_band/{machine_id}/{machine_type}/{production}/{is_first_production}/{line_product_station}/{current_machine_allocation_id?}", [
        ProductionCard\MachineAllocationController::class,
        "select_band"
    ])->name("select_band");

    Route::post("submit/{machine}/{master_production}", [
        ProductionCard\MachineAllocationController::class,
        "submit"
    ])->name("submit");

    Route::get("confirm/{machine}/{production}/{allocation}", [
        ProductionCard\MachineAllocationController::class,
        "confirm"
    ])->name("confirm");

    Route::post("confirm_submit/{machine}/{production}/{allocation}", [
        ProductionCard\MachineAllocationController::class,
        "confirm_submit"
    ])->name("confirm_submit");


});


Route::name( "allocation_cancel." )->prefix( "/allocation_cancel" )->group( function () {
    Route::get( "index/{allocation}/{production}", [
        ProductionCard\AllocationCancelController::class,
        "index"
    ] )->name( "index" );

} );

//Route::name("machine_reallocation.")->prefix("/machine_reallocation")->group(function () {
//    Route::get("index/{machine_allocation}", [
//        ProductionCard\MachineReallocationController::class,
//        "index"
//    ])->name("index");
//
//    Route::match(['get', 'post'], "select_band/{machine_id}/{machine_type}/{production}/{is_first_production}", [
//        ProductionCard\MachineReallocationController::class,
//        "select_band"
//    ])->name("select_band");
//});
Route::name("machine.")->prefix("/machine")->group(function () {

    Route::name("dashboard.")->prefix("/dashboard")->group(function () {
        Route::match(['get', 'post'], "index", [
            Machine\DashboardController::class,
            "index"
        ])->name("index");

        Route::get("view/{machine}", [Machine\DashboardController::class, "view"])->name("view");
        Route::get("short_link/{machine}", [Machine\DashboardController::class, "short_link"])->name("short_link");

    });


    # M 1: StartSetupController
    Route::prefix('start_setup')->name("start_setup.")->group(function () {
        Route::get("index/{machine}", [
            Machine\StartSetupController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\StartSetupController::class,
            "submit"
        ])->name("submit");
    });

    # M 2: StartOperationController
    Route::prefix('start_operation')->name("start_operation.")->group(function () {
        Route::get("index/{machine}", [
            Machine\StartOperationController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\StartOperationController::class,
            "submit"
        ])->name("submit");
    });
    Route::prefix('start_to_start')->name("start_to_start.")->group(function () {
        Route::get("index/{machine}", [
            Machine\StartToStartMachineController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\StartToStartMachineController::class,
            "submit"
        ])->name("submit");
    });

    # M 3: EndOfOperationController
    Route::prefix('end_of_operation')->name("end_of_operation.")->group(function () {
        Route::get("index/{machine}", [
            Machine\EndOfOperationController::class,
            "index"
        ])->name("index");

        Route::post("submit/{machine}", [
            Machine\EndOfOperationController::class,
            "submit"
        ])->name("submit");

        Route::get("set_production_form_amount/{machine}", [
            Machine\EndOfOperationController::class,
            "set_production_form_amount"
        ])->name("set_production_form_amount");

        Route::post("confirm_production_form_amount/{machine}", [
            Machine\EndOfOperationController::class,
            "confirm_production_form_amount"
        ])->name("confirm_production_form_amount");

    });

    # M 4: MakeFinalSettingController
    Route::prefix('make_final_setting')->name("make_final_setting.")->group(function () {
        Route::get("index/{machine}", [
            Machine\MakeFinalSettingController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\MakeFinalSettingController::class,
            "submit"
        ])->name("submit");
    });


//    # M 5: ConfirmQualityControlController
//    Route::prefix('confirm_quality_control')->name("confirm_quality_control.")->group(function () {
//        Route::get("index/{machine}", [
//            Machine\ConfirmQualityControlController::class,
//            "index"
//        ])->name("index");
//        Route::post("submit/{machine}", [
//            Machine\ConfirmQualityControlController::class,
//            "submit"
//        ])->name("submit");
//    });


    # M 6: InjectionOfMaterialController
    Route::prefix('injection_of_material')->name("injection_of_material.")->group(function () {
        Route::get("index/{machine}", [
            Machine\InjectionOfMaterialController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\InjectionOfMaterialController::class,
            "submit"
        ])->name("submit");
    });


    # M 7: RegisterProductionController
    Route::prefix('register_production')->name("register_production.")->group(function () {
        Route::get("index/{machine}", [
            Machine\RegisterProductionController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\RegisterProductionController::class,
            "submit"
        ])->name("submit");
    });


    # M 8: StartTesting
    Route::prefix('start_testing')->name("start_testing.")->group(function () {
        Route::get("index/{machine}", [
            Machine\StartTestingController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\StartTestingController::class,
            "submit"
        ])->name("submit");
    });
    # M 9: EndOfTesting
    Route::prefix('end_of_testing')->name("end_of_testing.")->group(function () {
        Route::post("submit/{machine}", [
            Machine\EndOfTestingController::class,
            "submit"
        ])->name("submit");
    });
    # M 10: ConfirmTesting
    Route::prefix('confirm_testing')->name("confirm_testing.")->group(function () {
        Route::get("index/{machine}", [
            Machine\ConfirmTestingController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\ConfirmTestingController::class,
            "submit"
        ])->name("submit");
        Route::get("change_bom/{machine}", [
            Machine\ConfirmTestingController::class,
            "change_bom"
        ])->name("change_bom");
        Route::post("submit_change_bom/{machine}", [
            Machine\ConfirmTestingController::class,
            "submit_change_bom"
        ])->name("submit_change_bom");
    });



    # M 11: EndOfMachineAllocationController
    Route::prefix('end_of_machine_allocation')->name("end_of_machine_allocation.")->group(function () {
//        Route::get("index/{machine}", [
//            Machine\EndOfMachineAllocationController::class,
//            "index"
//        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\EndOfMachineAllocationController::class,
            "submit"
        ])->name("submit");
    });


    # M 11: MachineCardController
    Route::prefix('machine_card')->name("machine_card.")->group(function () {

        Route::post("submit/{machine}", [
            Machine\MachineCardController::class,
            "submit"
        ])->name("submit");
    });


    // M 91: RequestRawMaterialController
    Route::name( "request_raw_material." )->prefix( "/request_raw_material" )->group( function () {
        Route::get( "index/{machine}", [
            Machine\RequestRawMaterialController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Machine\RequestRawMaterialController::class,
            "submit"
        ] )->name( "submit" );

        Route::get( "select_material/{machine}", [
            Machine\RequestRawMaterialController::class,
            "select_material"
        ] )->name( "select_material" );

        Route::post( "submit_select_material/{machine}", [
            Machine\RequestRawMaterialController::class,
            "submit_select_material"
        ] )->name( "submit_select_material" );
    } );

    # M 92: OperatorController
    Route::prefix('operator')->name("operator.")->group(function () {
        Route::get("index/{machine}", [
            Machine\OperatorController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\OperatorController::class,
            "submit"
        ])->name("submit");
    });


    // M 93: MaterialDeliveryConfirmationController
    Route::name("material_delivery_confirmation.")->prefix("/material_delivery_confirmation")->group(function () {
        Route::get("index/{machine}", [
            Machine\MaterialDeliveryConfirmationController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\MaterialDeliveryConfirmationController::class,
            "submit"
        ])->name("submit");
    });
    // M 94: MaterialDeliveryRejectController
    Route::name("material_delivery_reject.")->prefix("/material_delivery_reject")->group(function () {
        Route::get("index/{machine}", [
            Machine\MaterialDeliveryRejectController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\MaterialDeliveryRejectController::class,
            "submit"
        ])->name("submit");
    });
    // M 95: MaterialReturnToWarehouseController
    Route::name("material_return_to_warehouse.")->prefix("/material_return_to_warehouse")->group(function () {
        Route::get("index/{machine}", [
            Machine\MaterialReturnToWarehouseController::class,
            "index"
        ])->name("index");
        Route::get("remove_product_from_list/{machine}/{product}", [
            Machine\MaterialReturnToWarehouseController::class,
            "remove_product_from_list"
        ])->name("remove_product_from_list");

        Route::get("reset_removed_product_from_list/{machine}", [
            Machine\MaterialReturnToWarehouseController::class,
            "reset_removed_product_from_list"
        ])->name("reset_removed_product_from_list");
        Route::post("submit_change_range/{machine}", [
            Machine\MaterialReturnToWarehouseController::class,
            "submit_change_range"
        ])->name("submit_change_range");

        Route::get("remaining_packing_form/{machine}", [
            Machine\MaterialReturnToWarehouseController::class,
            "remaining_packing_form"
        ])->name("remaining_packing_form");

        Route::post("submit_remaining_packing_form/{machine}/{warehouse}", [
            Machine\MaterialReturnToWarehouseController::class,
            "submit_remaining_packing_form"
        ])->name("submit_remaining_packing_form");

        Route::get("confirm/{machine}/{warehouse}", [
            Machine\MaterialReturnToWarehouseController::class,
            "confirm"
        ])->name("confirm");

        Route::post("submit_confirm/{machine}/{warehouse}", [
            Machine\MaterialReturnToWarehouseController::class,
            "submit_confirm"
        ])->name("submit_confirm");

        Route::get("print_new_packing/{machine}", [
            Machine\MaterialReturnToWarehouseController::class,
            "print_new_packing"
        ])->name("print_new_packing");

        Route::post("submit_print_new_packing/{machine}/{machine_allocation_modification}", [
            Machine\MaterialReturnToWarehouseController::class,
            "submit_print_new_packing"
        ])->name("submit_print_new_packing");


        Route::get("warehouse_handling/{machine}/{warehouse}", [
            Machine\MaterialReturnToWarehouseController::class,
            "warehouse_handling"
        ])->name("warehouse_handling");

        Route::get("add_packing_form/{machine_allocation_modification}/{machine}/{product}/{type}", [
            Machine\MaterialReturnToWarehouseController::class,
            "add_packing_form"
        ])->name("add_packing_form");


        Route::get("delete_one_of_packing_form/{machine}/{packing_form}/{modification_packing_form_id}", [
            Machine\MaterialReturnToWarehouseController::class,
            "delete_one_of_packing_form"
        ])->name("delete_one_of_packing_form");

        Route::get("set_remainder_consumed/{machine_allocation_modification}/{machine}/{product}", [
            Machine\MaterialReturnToWarehouseController::class,
            "set_remainder_consumed"
        ])->name("set_remainder_consumed");
        Route::get("show_packing_form_by_product/{machine_allocation_modification}/{machine}/{product}/{consumed_status_id}", [
            Machine\MaterialReturnToWarehouseController::class,
            "show_packing_form_by_product"
        ])->name("show_packing_form_by_product");

    });

    // M 96: MaterialReturnToWarehouseLogController
    Route::name("material_return_to_warehouse_log.")->prefix("/material_return_to_warehouse_log")->group(function () {
        Route::get("index/{machine}", [
            Machine\MaterialReturnToWarehouseLogController::class,
            "index"
        ])->name("index");

        Route::get("details/{machine}/{machine_allocation_modification}", [
            Machine\MaterialReturnToWarehouseLogController::class,
            "details"
        ])->name("details");

        Route::get("print_one_of_packing_form/{machine}/{packing_form}/{modification_packing_form_id}", [
            Machine\MaterialReturnToWarehouseLogController::class,
            "print_one_of_packing_form"
        ])->name("print_one_of_packing_form");

    });

    // M 97: AllocationCardController
//    Route::name( "allocation_card." )->prefix( "/allocation_card" )->group( function () {
//
//        Route::match( [ 'get', 'post' ], "index/{machine}/{allocation_id?}", [
//            Fabric\SpecialProduction\Machine\AllocationCardController::class,
//            "index"
//        ] )->name( "index" );
//
//        Route::get( "print/{machine}/{allocation}", [
//            Fabric\SpecialProduction\Machine\AllocationCardController::class,
//            "print"
//        ] )->name( "print" );
//
//        Route::get( "download/{machine}/{allocation}", [
//            Fabric\SpecialProduction\Machine\AllocationCardController::class,
//            "download"
//        ] )->name( "download" );
//
//    } );


//    // M 98: FinishedAllocationController
//    Route::name( "finished_allocation." )->prefix( "/finished_allocation" )->group( function () {
//        Route::get( "index/{machine}", [
//            Fabric\SpecialProduction\Machine\FinishedAllocationController::class,
//            "index"
//        ] )->name( "index" );
//    } );
    // M 99: LogController
    Route::name("log.")->prefix("/log")->group(function () {
        Route::get("index/{machine}", [Machine\LogController::class, "index"])->name("index");
    });



    // M-- : ControlSampleController
    Route::name("control_sample.")->prefix("/control_sample")->group(function () {

        Route::get("index/{machine}", [
            Machine\ControlSampleController::class,
            "index"
        ])->name("index");
        Route::post("submit/{machine}", [
            Machine\ControlSampleController::class,
            "submit"
        ])->name("submit");

        Route::get("print_download_form/{machine}/{type}", [
            Machine\ControlSampleController::class,
            "print_download_form"
        ])->name("print_download_form");

        Route::get("print_download_form/{machine}/{type}", [
            Machine\ControlSampleController::class,
            "print_download_form"
        ])->name("print_download_form");

    });

});


