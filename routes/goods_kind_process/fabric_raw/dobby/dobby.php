<?php


use App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby;

Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
    Route::get( "view_card/{production}", [
        Dobby\ProductionCard\DashboardController::class,
        "view_card"
    ] )->name( "view_card" );
} );

Route::name( "machine_allocation." )->prefix( "/machine_allocation" )->group( function () {
    Route::get( "index/{production}/{machine_type}", [ Dobby\ProductionCard\MachineAllocationController::class, "index" ] )->name( "index" );

    Route::match( [ 'get', 'post' ], "select_band/{machine_id}/{machine_type}/{production}/{is_first_production}", [
        Dobby\ProductionCard\MachineAllocationController::class,
        "select_band"
    ] )->name( "select_band" );

    Route::get( "select_other_production/{machine}/{production}", [
        Dobby\ProductionCard\MachineAllocationController::class,
        "select_other_production"
    ] )->name( "select_other_production" );

    Route::post( "select_band_submit/{machine}/{production}", [
        Dobby\ProductionCard\MachineAllocationController::class,
        "select_band_submit"
    ] )->name( "select_band_submit" );

    Route::post( "confirm_submit/{machine}", [
        Dobby\ProductionCard\MachineAllocationController::class,
        "confirm_submit"
    ] )->name( "confirm_submit" );

    // ورودی
    Route::get( "select_input_line/{machine}/{is_edit}", [
        Dobby\ProductionCard\MachineAllocationController::class,
        "select_input_line"
    ] )->name( "select_input_line" );
    Route::get( "get_allocation_different/{allocation}", [
        Dobby\ProductionCard\MachineAllocationController::class,
        "get_allocation_different"
    ] )->name( "get_allocation_different" );

} );

Route::name( "allocation_cancel." )->prefix( "/allocation_cancel" )->group( function () {
    Route::get( "index/{production}/{machine}", [
        Dobby\ProductionCard\AllocationCancelController::class,
        "index"
    ] )->name( "index" );

} );

Route::name( "machine." )->prefix( "/machine" )->group( function () {
    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [ Dobby\Machine\DashboardController::class, "index" ] )->name( "index" );
        Route::get( "view/{machine}", [ Dobby\Machine\DashboardController::class, "view" ] )->name( "view" );
        Route::get( "change_lot_confirmation/{machine}", [
            Dobby\Machine\DashboardController::class,
            "change_lot_confirmation"
        ] )->name( "change_lot_confirmation" );
    } );

    // M 1:
    Route::name( "begin_intro_change_lot." )->prefix( "/begin_intro_change_lot" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\BeginIntroChangeLotController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\BeginIntroChangeLotController::class, "submit" ] )->name( "submit" );

    } );
    // M 3:
    Route::name( "end_of_intro_change_lot." )->prefix( "/end_of_intro_change_lot" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\EndOfIntroChangeLotController::class, "submit" ] )->name( "submit" );
    } );
    // M 4:
    Route::name( "end_of_step2_change_lot." )->prefix( "/end_of_step2_change_lot" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\EndOfStep2ChangeLogController::class, "submit" ] )->name( "submit" );
    } );
    // M 5:
    Route::name( "begin_warping." )->prefix( "/begin_warping" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\BeginWarpingController::class, "submit" ] )->name( "submit" );
    } );
    // M 6:
    Route::name( "end_warping." )->prefix( "/end_warping" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\EndWarpingController::class, "submit" ] )->name( "submit" );
    } );
    // M 7:
    Route::name( "begin_knotting." )->prefix( "/begin_knotting" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\BeginKnottingController::class, "submit" ] )->name( "submit" );
    } );
    // M 8:
    Route::name( "end_of_knotting." )->prefix( "/end_of_knotting" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\EndOfKnottingController::class, "submit" ] )->name( "submit" );
    } );
    // M 9:
    Route::name( "begin_pinning." )->prefix( "/begin_pinning" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\BeginPinningController::class, "submit" ] )->name( "submit" );
    } );
    // M 10:
    Route::name( "end_of_pinning." )->prefix( "/end_of_pinning" )->group( function () {
        Route::post( "submit/{machine}", [ Dobby\Machine\EndOfPinningController::class, "submit" ] )->name( "submit" );
    } );
    // M 11:
    Route::name( "launch_change_lot." )->prefix( "/launch_change_lot" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\LaunchChangeLotController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\LaunchChangeLotController::class, "submit" ] )->name( "submit" );
    } );
    // M 12:
    Route::name( "confirm_quality_control." )->prefix( "/confirm_quality_control" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\ConfirmQualityControlController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\ConfirmQualityControlController::class,
            "submit"
        ] )->name( "submit" );
        Route::get( "get_contour/{machine}", [
            Dobby\Machine\ConfirmQualityControlController::class,
            "get_contour"
        ] )->name( "get_contour" );
    } );
    // M 13:
    Route::name( "reject_quality_control_knotting." )->prefix( "/reject_quality_control_knotting" )->group( function () {
        Route::post( "submit/{machine}", [
            Dobby\Machine\RejectQualityControlKnottingController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 14:
    Route::name( "launch_shift." )->prefix( "/launch_shift" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\LaunchShiftController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\LaunchShiftController::class, "submit" ] )->name( "submit" );
    } );
    // M 15:
    Route::name( "change_yarn_lot." )->prefix( "/change_yarn_lot" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\ChangeYarnLotController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\ChangeYarnLotController::class, "submit" ] )->name( "submit" );
    } );
    // M 16:
    Route::name( "request_change_warps." )->prefix( "/request_change_warps" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\RequestChangeWarpsController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\RequestChangeWarpsController::class, "submit" ] )->name( "submit" );
    } );
    // M 17:
    Route::name( "begin_change_warps." )->prefix( "/begin_change_warps" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\BeginChangeWarpsController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\BeginChangeWarpsController::class, "submit" ] )->name( "submit" );
    } );
    // M 18:
    Route::name( "end_of_change_warps." )->prefix( "/end_of_change_warps" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\EndOfChangeWarpsController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\EndOfChangeWarpsController::class, "submit" ] )->name( "submit" );
    } );
    // M 19:
    Route::name( "begin_warping_for_change_warps." )->prefix( "/begin_warping_for_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\BeginWarpingForChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\BeginWarpingForChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 20:
    Route::name( "end_of_warping_for_change_warps." )->prefix( "/end_of_warping_for_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\EndOfWarpingForChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\EndOfWarpingForChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 21:
    Route::name( "warps_delivery_confirmation." )->prefix( "/warps_delivery_confirmation" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\WarpsDeliveryConfirmationController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\WarpsDeliveryConfirmationController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 22:
    Route::name( "warps_delivery_reject." )->prefix( "/warps_delivery_reject" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\WarpsDeliveryRejectController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [ Dobby\Machine\WarpsDeliveryRejectController::class, "submit" ] )->name( "submit" );
    } );
    // M 23:
    Route::name( "log." )->prefix( "/log" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\LogController::class, "index" ] )->name( "index" );
    } );
    // M 24:
    Route::name( "declaration_end_of_warps." )->prefix( "/declaration_end_of_warps" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\DeclarationEndOfWarpsController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\DeclarationEndOfWarpsController::class,
            "submit"
        ] )->name( "submit" );
        Route::post( "submit_type2/{machine}", [
            Dobby\Machine\DeclarationEndOfWarpsController::class,
            "submit_type2"
        ] )->name( "submit_type2" );
    } );
    // M 26:
    Route::name( "change_yarn_for_change_design." )->prefix( "/change_yarn_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\ChangeYarnForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\ChangeYarnForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 27:
    Route::name( "begin_change_warps_for_change_design." )->prefix( "/begin_change_warps_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\BeginChangeWarpsForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\BeginChangeWarpsForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 28:
    Route::name( "end_of_change_warps_for_change_design." )->prefix( "/end_of_change_warps_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\EndOfChangeWarpsForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\EndOfChangeWarpsForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 29:
    Route::name( "begin_warping_for_change_design." )->prefix( "/begin_warping_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\BeginWarpingForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\BeginWarpingForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 30:
    Route::name( "end_of_warping_for_change_design." )->prefix( "/end_of_warping_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\EndOfWarpingForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\EndOfWarpingForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 31:
    Route::name( "launch_shift_for_change_design." )->prefix( "/launch_shift_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\LaunchShiftForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\LaunchShiftForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 32:
    Route::name( "production_card_stop_order." )->prefix( "/production_card_stop_order" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\ProductionCardStopOrderController::class, "index" ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\ProductionCardStopOrderController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 33:
    Route::name( "begin_change_yarn_for_stop_order." )->prefix( "/begin_change_yarn_for_stop_order" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\BeginChangeYarnForStopOrderController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\BeginChangeYarnForStopOrderController::class,
            "submit"
        ] )->name( "submit" );
        Route::get( "confirmation/{machine}", [
            Dobby\Machine\BeginChangeYarnForStopOrderController::class,
            "confirmation"
        ] )->name( "confirmation" );

    } );
    // M 34:
    Route::name( "end_of_change_yarn_for_stop_order." )->prefix( "/end_of_change_yarn_for_stop_order" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\EndOfChangeYarnForStopOrderController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\EndOfChangeYarnForStopOrderController::class,
            "submit"
        ] )->name( "submit" );
        Route::get( "confirmation/{machine}", [
            Dobby\Machine\EndOfChangeYarnForStopOrderController::class,
            "confirmation"
        ] )->name( "confirmation" );
    } );

    // M 34:
    Route::name( "fabric_profile_card." )->prefix( "/fabric_profile_card" )->group( function () {
        Route::get( "index/{machine}", [ Dobby\Machine\FabricProfileCardController::class, "index" ] )->name( "index" );
        Route::get( "print/{machine}", [
            Dobby\Machine\FabricProfileCardController::class,
            "confirmation"
        ] )->name( "confirmation" );
    } );

    // M 35:
    Route::name( "preparation_for_pinning." )->prefix( "/preparation_for_pinning" )->group( function () {
        Route::post( "submit/{machine}", [
            Dobby\Machine\PreparationForPiningController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 36:
    Route::name( "begin_warps_extraction." )->prefix( "/begin_warps_extraction" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\BeginWarpsExtractionController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\BeginWarpsExtractionController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 37:
    Route::name( "end_of_warps_extraction." )->prefix( "/end_of_warps_extraction" )->group( function () {
        Route::get( "index/{machine}", [
            Dobby\Machine\EndOfWarpsExtractionController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Dobby\Machine\EndOfWarpsExtractionController::class,
            "submit"
        ] )->name( "submit" );
        Route::post( "submit_type2/{machine}", [
            Dobby\Machine\EndOfWarpsExtractionController::class,
            "submit_type2"
        ] )->name( "submit_type2" );
    } );

    // M 38:
    Route::name( "warps_delivery_to_warehouse." )->prefix( "/warps_delivery_to_warehouse" )->group( function () {
        Route::get( "index/{machine}/{form}", [
            Dobby\Machine\WarpsDeliveryToWarehouseController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}/{form}", [
            Dobby\Machine\WarpsDeliveryToWarehouseController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 39:
    Route::name( "request_change_warps_cancel." )->prefix( "/request_change_warps_cancel" )->group( function () {
        Route::post( "submit/{machine}", [
            Dobby\Machine\RequestChangeWarpsCancelController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 40:
    Route::name( "edit_log." )->prefix( "/edit_log" )->group( function () {
        Route::get( "index/{machine}/{machine_log}", [
            Dobby\Machine\EditLogController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}/{machine_log}", [
            Dobby\Machine\EditLogController::class,
            "submit"
        ] )->name( "submit" );
    } );
} );

Route::name( "production_form." )->prefix( "/production_form" )->group( function () {
    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [
            Dobby\ProductionForm\DashboardController::class,
            "index"
        ] )->name( "index" );
        Route::get( "view/{production_form}", [ Dobby\ProductionForm\DashboardController::class, "view" ] )->name( "view" );
    } );
    Route::name( "fabric_extraction." )->prefix( "/fabric_extraction" )->group( function () {
        Route::get( "index/{production_form}", [
            Dobby\ProductionForm\FabricExtractionController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form}", [
            Dobby\ProductionForm\FabricExtractionController::class,
            "submit"
        ] )->name( "submit" );
    } );

    Route::name( "finishing_fabric_extraction." )->prefix( "/finishing_fabric_extraction" )->group( function () {
        Route::get( "index/{production_form}", [
            Dobby\ProductionForm\FinishingFabricExtractionController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form}", [
            Dobby\ProductionForm\FinishingFabricExtractionController::class,
            "submit"
        ] )->name( "submit" );
    } );

    Route::name( "fabric_extraction_for_stop_order." )->prefix( "/fabric_extraction_for_stop_order" )->group( function () {
        Route::get( "index/{production_form}", [
            Dobby\ProductionForm\FabricExtractionForStopOrderController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form}", [
            Dobby\ProductionForm\FabricExtractionForStopOrderController::class,
            "submit"
        ] )->name( "submit" );
    } );

    Route::name( "grading." )->prefix( "/grading" )->group( function () {
        Route::get( "index/{production_form_item}", [
            Dobby\ProductionForm\GradingController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form_item}", [
            Dobby\ProductionForm\GradingController::class,
            "submit"
        ] )->name( "submit" );
        Route::get( "section/{production_form_item}", [
            Dobby\ProductionForm\GradingController::class,
            "section"
        ] )->name( "section" );
        Route::post( "submit_section/{production_form_item}", [
            Dobby\ProductionForm\GradingController::class,
            "submit_section"
        ] )->name( "submit_section" );
        Route::post( "end_of_section/{production_form_item}", [
            Dobby\ProductionForm\GradingController::class,
            "end_of_section"
        ] )->name( "end_of_section" );
        Route::get( "delete_section/{production_form_item}/{FabricRaw_grading}", [
            Dobby\ProductionForm\GradingController::class,
            "delete_section"
        ] )->name( "delete_section" );
    } );

    Route::name( "grading_cancel." )->prefix( "/grading_cancel" )->group( function () {

        Route::post( "submit/{production_form_item}", [
            Dobby\ProductionForm\GradingCancelController::class,
            "submit"
        ] )->name( "submit" );

    } );

    Route::name( "register_packing_carrier." )->prefix( "/register_packing_carrier" )->group( function () {
        Route::get( "index/{production_form_item}", [
            ProductionForm\RegisterPackingCarrierController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form_item}", [
            ProductionForm\RegisterPackingCarrierController::class,
            "submit"
        ] )->name( "submit" );
    } );

} );

Route::name( "packing." )->prefix( "/packing" )->group( function () {

    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [ Dobby\Packing\DashboardController::class, "index" ] )->name( "index" );
        Route::get( "view/{packing_form}", [ Dobby\Packing\DashboardController::class, "view" ] )->name( "view" );
        Route::get( "delivery_to_warehouse/{packing_form}", [
            Dobby\Packing\DashboardController::class,
            "delivery_to_warehouse"
        ] )->name( "delivery_to_warehouse" );
    } );
    Route::name( "change_carrier_code." )->prefix( "/change_carrier_code" )->group( function () {
        Route::get( "index/{packing_form}", [
            Dobby\Packing\ChangeCarrierCodeController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{packing_form}", [
            Dobby\Packing\ChangeCarrierCodeController::class,
            "submit"
        ] )->name( "submit" );
    } );
    Route::name( "fabric_waiting_for_packing." )->prefix( "/fabric_waiting_for_packing" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [
            Dobby\Packing\FabricWaitingForPackingController::class,
            "index"
        ] )->name( "index" );
        Route::get( "view/{FabricRaw_grading}", [
            Dobby\Packing\FabricWaitingForPackingController::class,
            "view"
        ] )->name( "view" );
        Route::post( "submit/{FabricRaw_grading}", [
            Dobby\Packing\FabricWaitingForPackingController::class,
            "submit"
        ] )->name( "submit" );
        Route::get ( "other_form/{FabricRaw_grading}", [
            Dobby\Packing\FabricWaitingForPackingController::class,
            "other_form"
        ] )->name( "other_form" );
        Route::get ( "view_other_form/{FabricRaw_grading}/{packing_form}", [
            Dobby\Packing\FabricWaitingForPackingController::class,
            "view_other_form"
        ] )->name( "view_other_form" );
    } );

    Route::name( "cancel_packing_form." )->prefix( "/cancel_packing_form" )->group( function () {

        Route::post( "submit/{packing_form}", [
            Dobby\Packing\CancelPackingFormController::class,
            "submit"
        ] )->name( "submit" );
    } );

    Route::name( "cancel_packing_form_item." )->prefix( "/cancel_packing_form_item" )->group( function () {
        Route::get( "index/{packing_form}/{packing_form_item}", [
            Dobby\Packing\CancelPackingFormItemController::class,
            "index"
        ] )->name( "index" );
    } );

    Route::name( "delivery_to_warehouse." )->prefix( "/delivery_to_warehouse" )->group( function () {
        Route::get( "get_nosa_code/{packing_form}", [
            Dobby\Packing\DeliveryToWarehouseController::class,
            "get_nosa_code"
        ] )->name( "get_nosa_code" );
        Route::post( "submit_nosa_code/{packing_form}", [
            Dobby\Packing\DeliveryToWarehouseController::class,
            "submit_nosa_code"
        ] )->name( "submit_nosa_code" );
        Route::post( "submit/{packing_form}", [
            Dobby\Packing\DeliveryToWarehouseController::class,
            "submit"
        ] )->name( "submit" );
    } );
} );




