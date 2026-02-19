<?php

use App\Http\Controllers\HR\PostController;
use App\Http\Controllers\OilChange\AdminController;
use App\Http\Controllers\OilChange\HomeController;
use App\Http\Controllers\OilChange\OilReportController;

Route::name( "admin." )->prefix( "/admin_dashboard" )->group( function () {
    Route::get( "index", [ AdminController::class, "index" ] )->name( "index" );
    Route::get( "create", [ AdminController::class, "create" ] )->name( "create" );
    Route::post( "store", [ AdminController::class, "store" ] )->name( "store" );
    Route::get( "edit/{customer}", [ AdminController::class, "edit" ] )->name( "edit" );
    Route::post( "update/{customer}", [ AdminController::class, "update" ] )->name( "update" );
    Route::get( "destroy/{customer}", [ AdminController::class, "destroy" ] )->name( "destroy" );
} );
Route::name( "home." )->prefix( "/oil_dashboard" )->group( function () {

    Route::get( "index/{car?}", [ HomeController::class, "index" ] )->name( "index" );
    Route::post( "store", [ HomeController::class, "store" ] )->name( "store" );

    Route::get( "create_service/{car}/{current_km}/{service?}", [
        HomeController::class,
        "create_service"
    ] )->name( "create_service" );
    Route::post( "store_service/{car}/{current_km}", [
        HomeController::class,
        "store_service"
    ] )->name( "store_service" );

    Route::get( "create_car/{car}/{current_km}", [ HomeController::class, "create_car" ] )->name( "create_car" );
    Route::post( "store_car/{car}/{current_km}", [ HomeController::class, "store_car" ] )->name( "store_car" );
    Route::get( "service_preview/{car}/{service}", [
        HomeController::class,
        "service_preview"
    ] )->name( "service_preview" );
    Route::post( "service_confirm/{car}/{service}", [
        HomeController::class,
        "service_confirm"
    ] )->name( "service_confirm" );

} );
Route::name( "oil.report." )->prefix( "/oil_report" )->group( function () {
    Route::get( "index", [ OilReportController::class, "index" ] )->name( "index" );
    Route::post( "show", [ OilReportController::class, "show" ] )->name( "show" );
} );
