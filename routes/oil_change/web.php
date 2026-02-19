<?php

use App\Http\Controllers\OilChange\PublicController;
use App\Http\Controllers\OilChange\GPSController;

Route::name( "public." )->prefix( "/service" )->group( function () {
    Route::get( "index", [ PublicController::class, "index" ] )->name( "index" );

    Route::get( "set_km/{key}", [ PublicController::class, "set_km" ] )->name( "set_km" );
    Route::post( "store_current_km/{car}/{key}", [ PublicController::class, "store_current_km" ] )->name( "store_current_km" );
    Route::get( "show_service/{car}/{key}", [ PublicController::class, "show_service" ] )->name( "show_service" );

    Route::get( "send_sms", [ PublicController::class, "send_sms" ] )->name( "send_sms" );

    Route::get( "create", [ PublicController::class, "create" ] )->name( "create" );
    Route::post( "store", [ PublicController::class, "store" ] )->name( "store" );
    Route::get( "edit/{customer}", [ PublicController::class, "edit" ] )->name( "edit" );
    Route::post( "update/{customer}", [ PublicController::class, "update" ] )->name( "update" );
    Route::get( "destroy/{customer}", [ PublicController::class, "destroy" ] )->name( "destroy" );
} );


Route::name( "gps." )->prefix( "/gps" )->group( function () {
    Route::get( "update_location/{tracker_id}/{lat}/{long}", [ GPSController::class, "update_location" ] )->name( "update_location" );
});
Route::get( "C{key}", [ PublicController::class, "set_km" ] )->name( "set_km" );
