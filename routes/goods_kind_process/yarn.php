<?php

use App\Http\Controllers\GoodsKindProcess\Yarn\ProductionFrom\ImplementationPeriodFormController;
use App\Http\Controllers\GoodsKindProcess\Yarn\DashboardController;

Route::name( "production_form.implementation_period_form." )->prefix( "/implementation_period_form" )->group( function () {
    Route::get( "index", [ ImplementationPeriodFormController::class, "index" ] )->name( "index" );
    Route::post( "submit", [ ImplementationPeriodFormController::class, "submit" ] )->name( "submit" );
    Route::get( "complete_form/{product}", [ ImplementationPeriodFormController::class, "complete_form" ] )->name( "complete_form" );
    Route::post( "submit_complete_form/{product}", [ ImplementationPeriodFormController::class, "submit_complete_form" ] )->name( "submit_complete_form" );
    Route::get( "show_form/{form}", [ ImplementationPeriodFormController::class, "show_form" ] )->name( "show_form" );
    Route::post( "submit_form/{form}", [ ImplementationPeriodFormController::class, "submit_form" ] )->name( "submit_form" );
} );



Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
    Route::get( "index", [ DashboardController::class, "index" ] )->name( "index" );
    Route::get( "show_form/{form}", [ DashboardController::class, "show_form" ] )->name( "show_form" );
    Route::get( "confirm_warehouse/{form}", [ DashboardController::class, "confirm_warehouse" ] )->name( "confirm_warehouse" );
   } );
