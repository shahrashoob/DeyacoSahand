<?php

use App\Http\Controllers\GoodsKindProcess\Fabric;

Route::name( "production_card." )->prefix( "/production_card" )->group( function () {

    Route::get( "view_card/{production}/{back_url_type?}", [
        Fabric\ProductionCardController::class,
        "view_card"
    ] )->name( "view_card" );


    Route::name("finished_allocation.")->prefix("/finished_allocation")->group(function () {
        Route::get("index/{production}", [
            Fabric\ProductionCard\FinishedAllocationController::class,
            "index"
        ])->name("index");
        Route::get("allocation_input/{production}/{allocation}", [
            Fabric\ProductionCard\FinishedAllocationController::class,
            "allocation_input"
        ])->name("allocation_input");
        Route::get("allocation_data/{production}/{allocation}", [
            Fabric\ProductionCard\FinishedAllocationController::class,
            "allocation_data"
        ])->name("allocation_data");
    });

    Route::name("terminate_production.")->prefix("/terminate_production")->group(function () {

        Route::get("index/{production}", [
            Fabric\ProductionCard\TerminateProductionController::class,
            "index"
        ])->name("index");

        Route::post("submit/{production}", [
            Fabric\ProductionCard\TerminateProductionController::class,
            "submit"
        ])->name("submit");

    });
} );


Route::name( "allocation_cancel." )->prefix( "/allocation_cancel" )->group( function () {
    Route::get( "index/{allocation}/{production}", [
        Fabric\ProductionCard\AllocationCancelController::class,
        "index"
    ] )->name( "index" );

} );


Route::name( "machine_allocation." )->prefix( "/machine_allocation" )->group( function () {
    Route::get( "index/{production}", [
        Fabric\ProductionCard\MachineAllocationController::class,
        "index"
    ] )->name( "index" );
    Route::get( "reallocation/{production}/{machine_allocation}", [
        Fabric\ProductionCard\MachineAllocationController::class,
        "reallocation"
    ] )->name( "reallocation" );

    Route::post( "select_machine_type/{production}/{machine_type}/{line_product_station}/{machine_allocation_id}", [
        Fabric\ProductionCard\MachineAllocationController::class,
        "select_machine_type"
    ] )->name( "select_machine_type" );

} );



Route::name( "production_form." )->prefix( "/production_form" )->group( function () {

    Route::get( "view/{production_form}", [ Fabric\ProductionFormController::class, "view" ] )->name( "view" );
    Route::get( "download_form/{production_form}", [ Fabric\ProductionFormController::class, "download_form" ] )->name( "download_form" );
    Route::get( "direct_print/{production_form}", [ Fabric\ProductionFormController::class, "direct_print" ] )->name( "direct_print" );
//
//    Route::name( "implementation_period_form." )->prefix( "/implementation_period_form" )->group( function () {
//        Route::get( "index", [ Warps\ProductionFrom\ImplementationPeriodFormController::class, "index" ] )->name( "index" );
//        Route::post( "submit", [
//            Warps\ProductionFrom\ImplementationPeriodFormController::class,
//            "submit"
//        ] )->name( "submit" );
//        Route::get( "complete_form/{product}/{machine_warps_id}", [
//            Warps\ProductionFrom\ImplementationPeriodFormController::class,
//            "complete_form"
//        ] )->name( "complete_form" );
//        Route::post( "submit_complete_form/{product}/{machine_warps_id}", [
//            Warps\ProductionFrom\ImplementationPeriodFormController::class,
//            "submit_complete_form"
//        ] )->name( "submit_complete_form" );
//        Route::get( "show_form/{form}", [
//            Warps\ProductionFrom\ImplementationPeriodFormController::class,
//            "show_form"
//        ] )->name( "show_form" );
//        Route::post( "submit_form/{form}", [
//            Warps\ProductionFrom\ImplementationPeriodFormController::class,
//            "submit_form"
//        ] )->name( "submit_form" );
//    } );
//
//
//    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
//        Route::match( [ 'get', 'post' ], "index", [ Warps\ProductionFrom\DashboardController::class, "index" ] )->name( "index" );
//        Route::get( "show_form/{form}", [
//            Warps\ProductionFrom\DashboardController::class,
//            "show_form"
//        ] )->name( "show_form" );
//        Route::get( "confirm_warehouse/{form}", [
//            Warps\ProductionFrom\DashboardController::class,
//            "confirm_warehouse"
//        ] )->name( "confirm_warehouse" );
//    } );
//
//    Route::name( "edit_production_form." )->prefix( "/edit_production_form" )->group( function () {
//        Route::get( "index/{form}", [ Warps\ProductionFrom\EditProductionFromController::class, "index" ] )->name( "index" );
//        Route::post( "submit/{form}", [ Warps\ProductionFrom\EditProductionFromController::class, "submit" ] )->name( "submit" );
//
//    });


} );

