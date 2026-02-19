<?php


    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
        Route::match( [ 'get', 'post' ], "index", [ \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\DashboardController::class, "index" ] )->name( "index" );
        Route::get( "view/{design_form}", [ \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\DashboardController::class, "view" ] )->name( "view" );
    } );

    Route::name( "start_designing." )->prefix( "/start_designing" )->group( function () {
        Route::get( "index/{design_form}", [ \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\StartDesigning::class, "index" ] )->name( "index" );
        Route::post( "submit/{design_form}", [ \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\StartDesigning::class, "submit" ] )->name( "submit" );
    } );
    Route::name( "end_of_designing." )->prefix( "/end_of_designing" )->group( function () {
        Route::get( "index/{design_form}", [ \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\EndOfDesigning::class, "index" ] )->name( "index" );
        Route::post( "submit/{design_form}", [ \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\EndOfDesigning::class, "submit" ] )->name( "submit" );
    } );
    Route::name( "warps_delivery_confirmation." )->prefix( "/warps_delivery_confirmation" )->group( function () {
        Route::get( "index/{design_form}", [
            \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\WarpsDeliveryConfirmationController::class,
            "index"
        ] )->name( "index" );
        Route::post( "submit/{design_form}", [
            \App\Http\Controllers\GoodsKindProcess\FabricRaw\Dobby\DesignForm\WarpsDeliveryConfirmationController::class,
            "submit"
        ] )->name( "submit" );
    } );

