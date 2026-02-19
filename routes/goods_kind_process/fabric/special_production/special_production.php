<?php

use App\Http\Controllers\GoodsKindProcess\Fabric;

Route::name( "machine_allocation." )->prefix( "/machine_allocation" )->group( function () {
    Route::get( "index/{production}/{machine_type}", [
        Fabric\SpecialProduction\ProductionCard\MachineAllocationController::class,
        "index"
    ] )->name( "index" );

    Route::match( [ 'get', 'post' ], "select_band/{machine_id}/{machine_type}/{production}/{is_first_production}/{line_product_station?}", [
        Fabric\SpecialProduction\ProductionCard\MachineAllocationController::class,
        "select_band"
    ] )->name( "select_band" );

    Route::post( "confirm_submit/{machine}", [
        Fabric\SpecialProduction\ProductionCard\MachineAllocationController::class,
        "confirm_submit"
    ] )->name( "confirm_submit" );


} );


//Route::name( "allocation_cancel." )->prefix( "/allocation_cancel" )->group( function () {
//    Route::get( "index/{allocation}/{production}", [
//        Matthys\ProductionCard\AllocationCancelController::class,
//        "index"
//    ] )->name( "index" );
//
//} );


Route::name( "machine." )->prefix( "/machine" )->group( function () {

    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [
            Fabric\SpecialProduction\Machine\DashboardController::class,
            "index"
        ] )->name( "index" );

        Route::match( [ 'get', 'post' ], "view/{machine}", [ Fabric\SpecialProduction\Machine\DashboardController::class, "view" ] )->name( "view" );
        Route::get( "short_link/{machine}", [ Fabric\SpecialProduction\Machine\DashboardController::class, "short_link" ] )->name( "short_link" );

    } );


    # M 1: RegisterProductionController
    Route::prefix( 'register_production' )->name( "register_production." )->group( function () {
        Route::get( "index/{machine_allocation}", [
            Fabric\SpecialProduction\Machine\RegisterProductionController::class,
            "index"
        ] )->name( "index" );
    } );



    // M 97: AllocationCardController
    Route::name( "allocation_card." )->prefix( "/allocation_card" )->group( function () {

        Route::match( [ 'get', 'post' ], "index/{machine}/{allocation_id?}", [
            Fabric\SpecialProduction\Machine\AllocationCardController::class,
            "index"
        ] )->name( "index" );

        Route::get( "print/{machine}/{allocation}", [
            Fabric\SpecialProduction\Machine\AllocationCardController::class,
            "print"
        ] )->name( "print" );

        Route::get( "download/{machine}/{allocation}", [
            Fabric\SpecialProduction\Machine\AllocationCardController::class,
            "download"
        ] )->name( "download" );

    } );


//    // M 98: FinishedAllocationController
//    Route::name( "finished_allocation." )->prefix( "/finished_allocation" )->group( function () {
//        Route::get( "index/{machine}", [
//            Fabric\SpecialProduction\Machine\FinishedAllocationController::class,
//            "index"
//        ] )->name( "index" );
//    } );
    // M 99: LogController
    Route::name( "log." )->prefix( "/log" )->group( function () {
        Route::get( "index/{machine}", [ Fabric\SpecialProduction\Machine\LogController::class, "index" ] )->name( "index" );
    } );


} );

