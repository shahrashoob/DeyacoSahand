<?php

use App\Http\Controllers\GoodsKindProcess\Warps\KarlMayer;

Route::name( "machine_allocation." )->prefix( "/machine_allocation" )->group( function () {
    Route::get( "index/{production}/{machine_type}", [
        KarlMayer\ProductionCard\MachineAllocationController::class,
        "index"
    ] )->name( "index" );

    Route::match( [ 'get', 'post' ], "select_band/{machine_id}/{machine_type}/{production}/{is_first_production}", [
        KarlMayer\ProductionCard\MachineAllocationController::class,
        "select_band"
    ] )->name( "select_band" );

    Route::post( "confirm_submit/{machine}", [
        KarlMayer\ProductionCard\MachineAllocationController::class,
        "confirm_submit"
    ] )->name( "confirm_submit" );


//    Route::get( "create_new_channel/{machine}/{production}/{allocation_amount}", [
//        KarlMayer\ProductionCard\MachineAllocationController::class,
//        "create_new_channel"
//    ] )->name( "create_new_channel" );
//
//    Route::post( "store_new_channel/{machine}/{production}", [
//        KarlMayer\ProductionCard\MachineAllocationController::class,
//        "store_new_channel"
//    ] )->name( "store_new_channel" );
} );


Route::name( "allocation_cancel." )->prefix( "/allocation_cancel" )->group( function () {
    Route::get( "index/{allocation}/{production}", [
        KarlMayer\ProductionCard\AllocationCancelController::class,
        "index"
    ] )->name( "index" );

} );


Route::name( "machine." )->prefix( "/machine" )->group( function () {

    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [
            KarlMayer\Machine\DashboardController::class,
            "index"
        ] )->name( "index" );

        Route::get( "view/{machine}", [ KarlMayer\Machine\DashboardController::class, "view" ] )->name( "view" );
        Route::get( "short_link/{machine}", [
            KarlMayer\Machine\DashboardController::class,
            "short_link"
        ] )->name( "short_link" );

    } );


    // M 1: ENDOFBeaming
    Route::name( "end_of_beaming." )->prefix( "/end_of_beaming" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\EndOfBeamingController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\EndOfBeamingController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 2: StartBeamingController
    Route::name( "start_beaming." )->prefix( "/start_beaming" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\StartBeamingController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\StartBeamingController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 3: EndOfShelvingController
    Route::name( "end_of_shelving." )->prefix( "/end_of_shelving" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\EndOfShelvingController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\EndOfShelvingController::class,
            "submit"
        ] )->name( "submit" );

    } );
    // M 4: EndOfWarpingController
    Route::name( "end_of_warping." )->prefix( "/end_of_warping" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\EndOfWarpingController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\EndOfWarpingController::class,
            "submit"
        ] )->name( "submit" );

    } );
    // M 5: StartWarpingController
    Route::name( "start_warping." )->prefix( "/start_warping" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\StartWarpingController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\StartWarpingController::class,
            "submit"
        ] )->name( "submit" );

    } );


    // M 6: MaterialDeliveryConfirmationController
    Route::name( "material_delivery_confirmation." )->prefix( "/material_delivery_confirmation" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\MaterialDeliveryConfirmationController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            KarlMayer\Machine\MaterialDeliveryConfirmationController::class,
            "submit"
        ] )->name( "submit" );
    } );


    // M 7: MaterialDeliveryRejectController
    Route::name( "material_delivery_reject." )->prefix( "/material_delivery_reject" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\MaterialDeliveryRejectController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            KarlMayer\Machine\MaterialDeliveryRejectController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 8: InjectionOfMaterialController
    Route::name( "injection_of_material." )->prefix( "/injection_of_material" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\InjectionOfMaterialController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            KarlMayer\Machine\InjectionOfMaterialController::class,
            "submit"
        ] )->name( "submit" );
    } );

    // M 9: MaterialReturnToWarehouseController
    Route::name( "material_return_to_warehouse." )->prefix( "/material_return_to_warehouse" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "index"
        ] )->name( "index" );
        Route::get( "remove_product_from_list/{machine}/{product}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "remove_product_from_list"
        ] )->name( "remove_product_from_list" );

        Route::get( "reset_removed_product_from_list/{machine}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "reset_removed_product_from_list"
        ] )->name( "reset_removed_product_from_list" );
        Route::post( "submit_change_range/{machine}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "submit_change_range"
        ] )->name( "submit_change_range" );

        Route::get( "remaining_packing_form/{machine}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "remaining_packing_form"
        ] )->name( "remaining_packing_form" );

        Route::post( "submit_remaining_packing_form/{machine}/{warehouse}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "submit_remaining_packing_form"
        ] )->name( "submit_remaining_packing_form" );

        Route::get( "confirm/{machine}/{warehouse}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "confirm"
        ] )->name( "confirm" );

        Route::post( "submit_confirm/{machine}/{warehouse}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "submit_confirm"
        ] )->name( "submit_confirm" );

        Route::get( "print_new_packing/{machine}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "print_new_packing"
        ] )->name( "print_new_packing" );

        Route::post( "submit_print_new_packing/{machine}/{machine_allocation_modification}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "submit_print_new_packing"
        ] )->name( "submit_print_new_packing" );


        Route::get( "warehouse_handling/{machine}/{warehouse}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "warehouse_handling"
        ] )->name( "warehouse_handling" );

        Route::get( "add_packing_form/{machine_allocation_modification}/{machine}/{product}/{type}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "add_packing_form"
        ] )->name( "add_packing_form" );


        Route::get( "delete_one_of_packing_form/{machine}/{packing_form}/{modification_packing_form_id}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "delete_one_of_packing_form"
        ] )->name( "delete_one_of_packing_form" );

        Route::get( "set_remainder_consumed/{machine_allocation_modification}/{machine}/{product}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "set_remainder_consumed"
        ] )->name( "set_remainder_consumed" );
        Route::get( "show_packing_form_by_product/{machine_allocation_modification}/{machine}/{product}/{consumed_status_id}", [
            KarlMayer\Machine\MaterialReturnToWarehouseController::class,
            "show_packing_form_by_product"
        ] )->name( "show_packing_form_by_product" );

    } );

    // M 10: WasteCollectionController
    Route::name( "waste_collection." )->prefix( "/waste_collection" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\WasteCollectionController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\WasteCollectionController::class,
            "submit"
        ] )->name( "submit" );

    } );

    // M 11: RequestRawMaterialController
    Route::name( "request_raw_material." )->prefix( "/request_raw_material" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\RequestRawMaterialController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{machine}", [
            KarlMayer\Machine\RequestRawMaterialController::class,
            "submit"
        ] )->name( "submit" );
        Route::get( "select_material/{machine}", [
            KarlMayer\Machine\RequestRawMaterialController::class,
            "select_material"
        ] )->name( "select_material" );

        Route::post( "submit_select_material/{machine}", [
            KarlMayer\Machine\RequestRawMaterialController::class,
            "submit_select_material"
        ] )->name( "submit_select_material" );
    } );


    // M 12: FailureShelvingController
    Route::name( "failure_shelving." )->prefix( "/failure_shelving" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\FailureShelvingController::class,
            "index"
        ] )->name( "index" );

        Route::post( "submit/{machine}", [
            KarlMayer\Machine\FailureShelvingController::class,
            "submit"
        ] )->name( "submit" );

    } );
    // M 13: MaterialReturnToWarehouseLogController
    Route::name( "material_return_to_warehouse_log." )->prefix( "/material_return_to_warehouse_log" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\MaterialReturnToWarehouseLogController::class,
            "index"
        ] )->name( "index" );

        Route::get( "details/{machine}/{machine_allocation_modification}", [
            KarlMayer\Machine\MaterialReturnToWarehouseLogController::class,
            "details"
        ] )->name( "details" );

        Route::get( "print_one_of_packing_form/{machine}/{packing_form}/{modification_packing_form_id}", [
            KarlMayer\Machine\MaterialReturnToWarehouseLogController::class,
            "print_one_of_packing_form"
        ] )->name( "print_one_of_packing_form" );

    } );

    // M 97: AllocationCardController
    Route::name( "allocation_card." )->prefix( "/allocation_card" )->group( function () {

        Route::match( [ 'get', 'post' ], "index/{machine}/{allocation_id?}", [
            KarlMayer\Machine\AllocationCardController::class,
            "index"
        ] )->name( "index" );

        Route::get( "print/{machine}/{allocation}", [
            KarlMayer\Machine\AllocationCardController::class,
            "print"
        ] )->name( "print" );

        Route::get( "download/{machine}/{allocation}", [
            KarlMayer\Machine\AllocationCardController::class,
            "download"
        ] )->name( "download" );

    } );


    // M 98: FinishedAllocationController
    Route::name( "finished_allocation." )->prefix( "/finished_allocation" )->group( function () {
        Route::get( "index/{machine}", [
            KarlMayer\Machine\FinishedAllocationController::class,
            "index"
        ] )->name( "index" );
        Route::get( "material_consumed_list/{machine}/{allocation}", [
            KarlMayer\Machine\FinishedAllocationController::class,
            "material_consumed_list"
        ] )->name( "material_consumed_list" );
    } );
    // M 99: LogController
    Route::name( "log." )->prefix( "/log" )->group( function () {
        Route::match(['get', 'post'],"index/{machine}", [ KarlMayer\Machine\LogController::class, "index" ] )->name( "index" );
    } );


} );

