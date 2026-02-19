<?php

use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\InjectionOfMaterialController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\MaterialReturnToWarehouseController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\RegisterBrandController;
use App\Http\Controllers\GoodsKindProcess\FabricRaw\Jacquard\Machine\ReLaunchController;

Route::name( "machine_allocation." )->prefix( "/machine_allocation" )->group( function () {

    Route::get( "index/{production}/{machine_type}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "index"
    ] )->name( "index" );

    Route::match( [ 'get', 'post' ], "select_band/{machine_id}/{machine_type}/{production}/{is_first_production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "select_band"
    ] )->name( "select_band" );

    Route::get( "select_other_production/{machine}/{production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "select_other_production"
    ] )->name( "select_other_production" );

    Route::get( "select_other_band_production/{machine}/{production}/{other_production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "select_other_band_production"
    ] )->name( "select_other_band_production" );

    Route::post( "select_band_submit/{machine}/{production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "select_band_submit"
    ] )->name( "select_band_submit" );

    Route::post( "confirm_submit/{machine}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "confirm_submit"
    ] )->name( "confirm_submit" );


    Route::get( "select_permutation/{machine}/{production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "select_permutation"
    ] )->name( "select_permutation" );

    Route::post( "submit_select_permutation/{machine}/{production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "submit_select_permutation"
    ] )->name( "submit_select_permutation" );

    // ورودی
    Route::get( "select_input_line/{machine}/{production}/{is_edit}/{permutation_select_number?}/{end_result_index?}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "select_input_line"
    ] )->name( "select_input_line" );
    Route::get( "get_allocation_different/{allocation}/{production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "get_allocation_different"
    ] )->name( "get_allocation_different" );

    Route::get( "create_new_channel/{machine}/{production}/{allocation_amount}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "create_new_channel"
    ] )->name( "create_new_channel" );

    Route::post( "store_new_channel/{machine}/{production}", [
        Jacquard\ProductionCard\MachineAllocationController::class,
        "store_new_channel"
    ] )->name( "store_new_channel" );



} );


Route::name( "allocation_cancel." )->prefix( "/allocation_cancel" )->group( function () {
    Route::get( "index/{allocation}/{production}", [
        Jacquard\ProductionCard\AllocationCancelController::class,
        "index"
    ] )->name( "index" );

} );


Route::name( "machine." )->prefix( "/machine" )->group( function () {

    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [
            Jacquard\Machine\DashboardController::class,
            "index"
        ] )->name( "index" );
        Route::get( "view/{machine}", [ Jacquard\Machine\DashboardController::class, "view" ] )->name( "view" );

        Route::get( "short_link/{machine}", [ Jacquard\Machine\DashboardController::class, "short_link" ] )->name( "short_link" );

        Route::get( "change_lot_confirmation/{machine}", [
            Jacquard\Machine\DashboardController::class,
            "change_lot_confirmation"
        ] )->name( "change_lot_confirmation" );

    } );


    //MachineOffNotification روت
     Route::name("off_notification.")->prefix("/off_notification")->group(function () {
        Route::get("index/{machine}", [
            Jacquard\Machine\MachineOffNotificationController::class,
            "index"
        ])->name("index");

        Route::post("submit/{machine}", [
            Jacquard\Machine\MachineOffNotificationController::class,
            "submit"
        ])->name("submit");
    });

     



    // M 1: EndOfChangeDesignController
    Route::name( "end_of_change_design." )->prefix( "/end_of_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "index"
        ] )->name( "index" );

        Route::get( "has_article_change/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "has_article_change"
        ] )->name( "has_article_change" );

        Route::get( "has_yarn_weft_change/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "has_yarn_weft_change"
        ] )->name( "has_yarn_weft_change" );
        Route::post( "submit_yarn_weft_change/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "submit_yarn_weft_change"
        ] )->name( "submit_yarn_weft_change" );

        Route::get( "has_weft_density_change/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "has_weft_density_change"
        ] )->name( "has_weft_density_change" );

        Route::get( "has_meter_change/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "has_meter_change"
        ] )->name( "has_meter_change" );

        Route::post( "submit_injection_of_material/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "submit_injection_of_material"
        ] )->name( "submit_injection_of_material" );

        Route::get( "complete/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "complete"
        ] )->name( "complete" );

        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfChangeDesignController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 2: EndOfProductionCardTextureController
    Route::name( "end_of_production_card_texture." )->prefix( "/end_of_production_card_texture" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfProductionCardTextureController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfProductionCardTextureController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 03: BeginWarpsExtractionController
    Route::name( "begin_warps_extraction_and_put." )->prefix( "/begin_warps_extraction_and_put" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\BeginWarpsExtractionAndPutController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginWarpsExtractionAndPutController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 04: EndOfWarpsExtractionAndPutController
    Route::name( "end_of_warps_extraction_and_put." )->prefix( "/end_of_warps_extraction_and_put" )->group( function () {

        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfWarpsExtractionAndPutController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfWarpsExtractionAndPutController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 05: WarpsDeliveryConfirmationController
    Route::name( "warps_delivery_confirmation." )->prefix( "/warps_delivery_confirmation" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\WarpsDeliveryConfirmationController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\WarpsDeliveryConfirmationController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 06: WarpsDeliveryRejectController
    Route::name( "warps_delivery_reject." )->prefix( "/warps_delivery_reject" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\WarpsDeliveryRejectController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\WarpsDeliveryRejectController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 07: WarpsDeliveryToWarehouseController
    Route::name( "warps_delivery_to_warehouse." )->prefix( "/warps_delivery_to_warehouse" )->group( function () {
        Route::get( "index/{machine}/{form}", [
            Jacquard\Machine\WarpsDeliveryToWarehouseController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}/{form}", [
            Jacquard\Machine\WarpsDeliveryToWarehouseController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 08: BeginWarpingForChangeDesignController
    Route::name( "begin_warping_for_change_design." )->prefix( "/begin_warping_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\BeginWarpingForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginWarpingForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 09: EndOfWarpingForChangeDesignController
    Route::name( "end_of_warping_for_change_design." )->prefix( "/end_of_warping_for_change_design" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfWarpingForChangeDesignController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfWarpingForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 10: EndOfPinningController
    Route::name( "begin_pinning." )->prefix( "/begin_pinning" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginPinningController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 11: EndOfPinningController
    Route::name( "end_of_pinning." )->prefix( "/end_of_pinning" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfPinningController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 12: BeginForChangeDesignController
    Route::name( "begin_for_change_design." )->prefix( "/begin_for_change_design" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginForChangeDesignController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 13: BeginLaunchController
    Route::name( "begin_launch." )->prefix( "/begin_launch" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginLaunchController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 14: EndOfLaunchController
    Route::name( "end_of_launch." )->prefix( "/end_of_launch" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfLaunchController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfLaunchController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 15:RequestChangeWarpsController
    Route::name( "request_change_warps." )->prefix( "/request_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\RequestChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\RequestChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 16: BeginChangeWarpsController
    Route::name( "begin_change_warps." )->prefix( "/begin_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\BeginChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 17: EndOfChangeWarpsController
    Route::name( "end_of_change_warps." )->prefix( "/end_of_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 18: BeginWarpingForChangeWarpsController
    Route::name( "begin_warping_for_change_warps." )->prefix( "/begin_warping_for_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\BeginWarpingForChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginWarpingForChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 19: EndOfWarpingForChangeWarpsController
    Route::name( "end_of_warping_for_change_warps." )->prefix( "/end_of_warping_for_change_warps" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfWarpingForChangeWarpsController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfWarpingForChangeWarpsController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 20:
    Route::name( "request_change_warps_cancel." )->prefix( "/request_change_warps_cancel" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\RequestChangeWarpsCancelController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 21:
    Route::name( "allocation_card." )->prefix( "/allocation_card" )->group( function () {

        Route::match( [ 'get', 'post' ], "index/{machine}/{allocation_id?}", [
            Jacquard\Machine\AllocationCardController::class,
            "index"
        ] )->name( "index" );

        Route::get( "print/{machine}/{allocation}", [
            Jacquard\Machine\AllocationCardController::class,
            "print"
        ] )->name( "print" );

        Route::get( "download/{machine}/{allocation}", [
            Jacquard\Machine\AllocationCardController::class,
            "download"
        ] )->name( "download" );

    } );

    // M 22:
    Route::name( "change_allocation_amount." )->prefix( "/change_allocation_amount" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\ChangeAllocationAmountController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\ChangeAllocationAmountController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 23: BeginCombingController
    Route::name( "begin_combing." )->prefix( "/begin_combing" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginCombingController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 24: EndOfCombingController
    Route::name( "end_of_combing." )->prefix( "/end_of_combing" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfCombingController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 25: BeginChangeMachineBarController
    Route::name( "begin_change_machine_bar." )->prefix( "/begin_change_machine_bar" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginChangeMachineBarController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 26: EndOfChangeMachineBarController
    Route::name( "end_of_change_machine_bar." )->prefix( "/end_of_change_machine_bar" )->group( function () {
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfChangeMachineBarController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 27: OperatorController
    Route::name( "operator." )->prefix( "/operator" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\OperatorController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            Jacquard\Machine\OperatorController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 28:
    Route::name( "machine_card." )->prefix( "/machine_card" )->group( function () {
        Route::match( [ 'get', 'post' ], "submit/{machine}", [
            Jacquard\Machine\MachineCardController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 29: FailureToLaunchController
    Route::name( "failure_to_launch." )->prefix( "/failure_to_launch" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\FailureToLaunchController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\FailureToLaunchController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 30
    Route::name( "begin_sampling." )->prefix( "/begin_sampling" )->group( function () {

        Route::post( "submit/{machine}", [
            Jacquard\Machine\BeginSamplingController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M31
    Route::name( "end_of_sampling." )->prefix( "/end_of_sampling" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\EndOfSamplingController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\EndOfSamplingController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 32: MaterialDeliveryConfirmationController
    Route::name( "material_delivery_confirmation." )->prefix( "/material_delivery_confirmation" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\MaterialDeliveryConfirmationController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\MaterialDeliveryConfirmationController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 33: MaterialDeliveryRejectController
    Route::name( "material_delivery_reject." )->prefix( "/material_delivery_reject" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\MaterialDeliveryRejectController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\MaterialDeliveryRejectController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 34: InjectionOfMaterialController
    Route::name( "injection_of_material." )->prefix( "/injection_of_material" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\InjectionOfMaterialController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\InjectionOfMaterialController::class,
            "submit"
        ] )->name( "submit" );
    } );
    // M 35: MaterialReturnToWarehouseController
    Route::name( "material_return_to_warehouse." )->prefix( "/material_return_to_warehouse" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "index"
        ] )->name( "index" );
        Route::get( "remove_product_from_list/{machine}/{product}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "remove_product_from_list"
        ] )->name( "remove_product_from_list" );

        Route::get( "reset_removed_product_from_list/{machine}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "reset_removed_product_from_list"
        ] )->name( "reset_removed_product_from_list" );

        Route::post( "submit_change_range/{machine}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "submit_change_range"
        ] )->name( "submit_change_range" );

        Route::get( "remaining_packing_form/{machine}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "remaining_packing_form"
        ] )->name( "remaining_packing_form" );

        Route::post( "submit_remaining_packing_form/{machine}/{warehouse}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "submit_remaining_packing_form"
        ] )->name( "submit_remaining_packing_form" );

        Route::get( "confirm/{machine}/{warehouse}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "confirm"
        ] )->name( "confirm" );

        Route::post( "submit_confirm/{machine}/{warehouse}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "submit_confirm"
        ] )->name( "submit_confirm" );

        Route::get( "print_new_packing/{machine}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "print_new_packing"
        ] )->name( "print_new_packing" );

        Route::post( "submit_print_new_packing/{machine}/{machine_allocation_modification}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "submit_print_new_packing"
        ] )->name( "submit_print_new_packing" );


        Route::get( "warehouse_handling/{machine}/{warehouse}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "warehouse_handling"
        ] )->name( "warehouse_handling" );

        Route::get( "add_packing_form/{machine_allocation_modification}/{machine}/{product}/{type}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "add_packing_form"
        ] )->name( "add_packing_form" );


        Route::get( "delete_one_of_packing_form/{machine}/{packing_form}/{modification_packing_form_id}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "delete_one_of_packing_form"
        ] )->name( "delete_one_of_packing_form" );

        Route::get( "set_remainder_consumed/{machine_allocation_modification}/{machine}/{product}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "set_remainder_consumed"
        ] )->name( "set_remainder_consumed" );
        Route::get( "show_packing_form_by_product/{machine_allocation_modification}/{machine}/{product}/{consumed_status_id}", [
            Jacquard\Machine\MaterialReturnToWarehouseController::class,
            "show_packing_form_by_product"
        ] )->name( "show_packing_form_by_product" );

    } );

    // M 36: WasteCollection
    Route::name( "waste_collection." )->prefix( "/waste_collection" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\WasteCollectionController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            Jacquard\Machine\WasteCollectionController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 37: ReLaunchController
    Route::name( "re_launch." )->prefix( "/re_launch" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\ReLaunchController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\ReLaunchController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 38: RequestRawMaterialController
    Route::name( "request_raw_material." )->prefix( "/request_raw_material" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\RequestRawMaterialController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\RequestRawMaterialController::class,
            "submit"
        ] )->name( "submit" );

        Route::get( "select_material/{machine}", [
            Jacquard\Machine\RequestRawMaterialController::class,
            "select_material"
        ] )->name( "select_material" );

        Route::post( "submit_select_material/{machine}", [
            Jacquard\Machine\RequestRawMaterialController::class,
            "submit_select_material"
        ] )->name( "submit_select_material" );
    } );

    // M 39: MachineFaultNotificationController
    Route::name( "machine_fault_notification." )->prefix( "/machine_fault_notification" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\MachineFaultNotificationController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\MachineFaultNotificationController::class,
            "submit"
        ] )->name( "submit" );

        Route::get( "machine_contour/{machine}", [
            Jacquard\Machine\MachineFaultNotificationController::class,
            "machine_contour"
        ] )->name( "machine_contour" );

        Route::post( "submit_machine_contour/{machine}", [
            Jacquard\Machine\MachineFaultNotificationController::class,
            "submit_machine_contour"
        ] )->name( "submit_machine_contour" );

    } );

    // M 40: MachineMaintenanceConfirmController
    Route::name( "machine_maintenance_confirm." )->prefix( "/machine_maintenance_confirm" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\MachineMaintenanceConfirmController::class,
            "index"
        ] )->name( "index" );
        Route::get( "confirm/{machine}/{maintenance}", [
            Jacquard\Machine\MachineMaintenanceConfirmController::class,
            "confirm"
        ] )->name( "confirm" );
        Route::get( "reject/{machine}/{maintenance}", [
            Jacquard\Machine\MachineMaintenanceConfirmController::class,
            "reject"
        ] )->name( "reject" );

    } );

    // M 41: ProductionChannelManagementController
    Route::name( "production_channel_management." )->prefix( "/production_channel_management" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\ProductionChannelManagementController::class,
            "index"
        ] )->name( "index" );
        Route::get( "update_to_produced/{machine}/{machine_production_channel}", [
            Jacquard\Machine\ProductionChannelManagementController::class,
            "update_to_produced"
        ] )->name( "update_to_produced" );
        Route::get( "update_to_canceled/{machine}/{machine_production_channel}", [
            Jacquard\Machine\ProductionChannelManagementController::class,
            "update_to_canceled"
        ] )->name( "update_to_canceled" );

    } );

    // M 42: RejectChangeDesignController
    Route::name( "failure_change_design." )->prefix( "/failure_change_design" )->group( function () {

        Route::get( "index/{machine}", [
            Jacquard\Machine\FailureChangeDesignController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            Jacquard\Machine\FailureChangeDesignController::class,
            "submit"
        ] )->name( "submit" );

    } );


    // M 43: MaterialReturnToWarehouseLogController
    Route::name( "material_return_to_warehouse_log." )->prefix( "/material_return_to_warehouse_log" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\MaterialReturnToWarehouseLogController::class,
            "index"
        ] )->name( "index" );

        Route::get( "details/{machine}/{machine_allocation_modification}", [
            Jacquard\Machine\MaterialReturnToWarehouseLogController::class,
            "details"
        ] )->name( "details" );
        Route::get( "print_one_of_packing_form/{machine}/{packing_form}/{modification_packing_form_id}", [
            Jacquard\Machine\MaterialReturnToWarehouseLogController::class,
            "print_one_of_packing_form"
        ] )->name( "print_one_of_packing_form" );
    } );




    // M 44: RegisterBrandController
    Route::name( "register_brand." )->prefix( "/register_brand" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\RegisterBrandController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            Jacquard\Machine\RegisterBrandController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 98: FinishedAllocationController
    Route::name( "finished_allocation." )->prefix( "/finished_allocation" )->group( function () {
        Route::get( "index/{machine}", [
            Jacquard\Machine\FinishedAllocationController::class,
            "index"
        ] )->name( "index" );

        Route::get( "material_consumed_list/{machine}/{allocation}", [
            Jacquard\Machine\FinishedAllocationController::class,
            "material_consumed_list"
        ] )->name( "material_consumed_list" );
    } );
    // M 99: LogController
    Route::name( "log." )->prefix( "/log" )->group( function () {
        Route::match(['get', 'post'], "index/{machine}", [ Jacquard\Machine\LogController::class, "index" ] )->name( "index" );

        Route::get( "view_input_log/{machine}/{current_machine_input}", [
            Jacquard\Machine\LogController::class,
            "view_input_log"
        ] )->name( "view_input_log" );
    } );


} );


Route::name( "production_form." )->prefix( "/production_form" )->group( function () {
    Route::name( "fabric_extraction." )->prefix( "/fabric_extraction" )->group( function () {
        Route::get( "index/{production_form}", [
            Jacquard\ProductionForm\FabricExtractionController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form}", [
            Jacquard\ProductionForm\FabricExtractionController::class,
            "submit"
        ] )->name( "submit" );
    } );
    Route::name( "extraction_item." )->prefix( "/extraction_item" )->group( function () {
        Route::get( "index/{production_form}", [
            Jacquard\ProductionForm\ExtractionItemController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{production_form}", [
            Jacquard\ProductionForm\ExtractionItemController::class,
            "submit"
        ] )->name( "submit" );
    } );
} );
