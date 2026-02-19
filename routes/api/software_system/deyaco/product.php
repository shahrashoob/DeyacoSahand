<?php

use App\Http\Controllers\Utility\SoftwareSystem;


Route::name("product_creation.")->prefix("product_creation")->group(function () {
    Route::name("new_form_api.")->prefix("new_form_api")->group(function () {
        Route::post( "submit", [SoftwareSystem\Deyaco\Product\ProductCreation\NewFormApiController::class, "submit"] )->name( "submit" );
    });
});