<?php

use App\Http\Controllers\Import\NosaController;
use App\Http\Controllers\Import\Product;
use App\Http\Controllers\Import\BOMController;
use App\Http\Controllers\Import\CustomerController;
use App\Http\Controllers\Import\LineController;
use App\Http\Controllers\Import\LineProductController;
use App\Http\Controllers\Import\WorkerController;
use App\Http\Controllers\Import\AccountBalanceController;
use App\Http\Controllers\Import;


Route::middleware( [ 'url_check:import.nosa.index' ] )->name( "nosa." )->prefix( "nosa" )->group( function () {
    Route::get( "index", [ NosaController::class, "index" ] )->name( "index" );
    Route::post( "upload", [ NosaController::class, "upload" ] )->name( "upload" );
    Route::get( "show", [ NosaController::class, "show" ] )->name( "show" );
    Route::post( "update", [ NosaController::class, "update" ] )->name( "update" );
    Route::get( "with_out_orderlist", [
        NosaController::class,
        "with_out_orderlist"
    ] )->name( "with_out_orderlist" );
} );

Route::name( "product." )->prefix( "/product" )->group( function () {
    Route::middleware( [ 'url_check:import.product.base_and_storage.index' ] )->name( "base_and_storage." )->prefix( "/base_and_storage" )->group( function () {
        Route::get( "index", [ Product\BaseAndStorageController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\BaseAndStorageController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\BaseAndStorageController::class, "show" ] )->name( "show" );
        Route::get( "update", [ Product\BaseAndStorageController::class, "update" ] )->name( "update" );
    } );

    Route::middleware( [ 'url_check:import.product.production.index' ] )->name( "production." )->prefix( "/production" )->group( function () {
        Route::get( "index", [ Product\ProductionController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\ProductionController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\ProductionController::class, "show" ] )->name( "show" );
        Route::get( "update", [ Product\ProductionController::class, "update" ] )->name( "update" );
    } );

    Route::middleware( [ 'url_check:import.product.pricing.index' ] )->name( "pricing." )->prefix( "/pricing" )->group( function () {
        Route::get( "index", [ Product\PricingController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\PricingController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\PricingController::class, "show" ] )->name( "show" );
        Route::get( "update", [ Product\PricingController::class, "update" ] )->name( "update" );
        Route::get( "get_sampling_product_pricing", [ Product\PricingController::class, "get_sampling_product_pricing" ] )->name( "get_sampling_product_pricing" );
        Route::post( "submit_sampling_product_pricing", [ Product\PricingController::class, "submit_sampling_product_pricing" ] )->name( "submit_sampling_product_pricing" );
    } );

    Route::middleware( [ 'url_check:import.product.purchase.index' ] )->name( "purchase." )->prefix( "/purchase" )->group( function () {
        Route::get( "index", [ Product\PurchaseController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\PurchaseController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\PurchaseController::class, "show" ] )->name( "show" );
        Route::get( "update", [ Product\PurchaseController::class, "update" ] )->name( "update" );
    } );
    Route::middleware( [ 'url_check:import.product.property.index' ] )->name( "property." )->prefix( "/property" )->group( function () {
        Route::get( "index", [ Product\PropertyController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\PropertyController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\PropertyController::class, "show" ] )->name( "show" );
        Route::get( "update", [ Product\PropertyController::class, "update" ] )->name( "update" );
        Route::get( "download_page", [ Product\PropertyController::class, "download_page" ] )->name( "download_page" );
        Route::post( "download_excel", [
            Product\PropertyController::class,
            "download_excel"
        ] )->name( "download_excel" );
    } );
    Route::middleware( [ 'url_check:import.product.lot_number.index' ] )->name( "lot_number." )->prefix( "/lot_number" )->group( function () {
        Route::get( "index", [ Product\LotNumberController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\LotNumberController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\LotNumberController::class, "show" ] )->name( "show" );
        Route::post( "update", [ Product\LotNumberController::class, "update" ] )->name( "update" );
    } );
    Route::middleware( [ 'url_check:import.product.bom.index' ] )->name( "bom." )->prefix( "/bom" )->group( function () {
        Route::get( "index", [ Product\BOMController::class, "index" ] )->name( "index" );
        Route::post( "upload", [ Product\BOMController::class, "upload" ] )->name( "upload" );
        Route::get( "show", [ Product\BOMController::class, "show" ] )->name( "show" );
        Route::post( "update", [ Product\BOMController::class, "update" ] )->name( "update" );
    } );
} );

Route::middleware( [ 'url_check:import.customer.index' ] )->name( "customer." )->prefix( "/customer" )->group( function () {
    Route::get( "index", [ CustomerController::class, "index" ] )->name( "index" );
    Route::post( "upload", [ CustomerController::class, "upload" ] )->name( "upload" );
    Route::get( "show", [ CustomerController::class, "show" ] )->name( "show" );
    Route::post( "update", [ CustomerController::class, "update" ] )->name( "update" );
} );

Route::middleware( [ 'url_check:import.line.index' ] )->name( "line." )->prefix( "/line" )->group( function () {
    Route::get( "index", [ LineController::class, "index" ] )->name( "index" );
    Route::post( "upload", [ LineController::class, "upload" ] )->name( "upload" );
    Route::get( "show", [ LineController::class, "show" ] )->name( "show" );
    Route::post( "update", [ LineController::class, "update" ] )->name( "update" );
} );

Route::middleware( [ 'url_check:import.line_product.index' ] )->name( "line_product." )->prefix( "/line_product" )->group( function () {
    Route::get( "index", [ LineProductController::class, "index" ] )->name( "index" );
    Route::post( "upload", [ LineProductController::class, "upload" ] )->name( "upload" );
    Route::get( "show", [ LineProductController::class, "show" ] )->name( "show" );
    Route::post( "update", [ LineProductController::class, "update" ] )->name( "update" );
});

Route::middleware( [ 'url_check:import.worker.index' ] )->name( "worker." )->prefix( "/worker" )->group( function () {
    Route::get( "index", [ WorkerController::class, "index" ] )->name( "index" );
    Route::post( "upload", [ WorkerController::class, "upload" ] )->name( "upload" );
    Route::get( "show", [ WorkerController::class, "show" ] )->name( "show" );
    Route::post( "update", [ WorkerController::class, "update" ] )->name( "update" );
});

Route::middleware( [ 'url_check:import.account_balance.index' ] )->name( "account_balance." )->prefix( "/account_balance" )->group( function () {

    Route::get( "index", [
        AccountBalanceController::class,
        "index"
    ] )->name( "index" );
    Route::post( "upload/{type}", [
        AccountBalanceController::class,
        "upload"
    ] )->name( "upload" );
    Route::get( "show/{type}", [
        AccountBalanceController::class,
        "show"
    ] )->name( "show" );
    Route::post( "update/{type}", [
        AccountBalanceController::class,
        "update"
    ] )->name( "update" );
});

Route::middleware( [ 'url_check:import.add_existing_products.index' ] )->name( "add_existing_products." )->prefix( "/add_existing_products" )->group( function () {

    Route::get( "index", [
        Import\AddingExistingProductsController::class,
        "index"
    ] )->name( "index" );

    Route::post( "upload", [
        Import\AddingExistingProductsController::class,
        "upload"
    ] )->name( "upload" );
    Route::get( "show", [
        Import\AddingExistingProductsController::class,
        "show"
    ] )->name( "show" );
    Route::get( "update", [
        Import\AddingExistingProductsController::class,
        "update"
    ] )->name( "update" );
} );

Route::middleware( [ 'url_check:import.warehouse_handling.index' ] )->name( "packing_form_handling." )->prefix( "/packing_form_handling" )->group( function () {

    Route::get( "index", [
        Import\PackingFormHandingController::class,
        "index"
    ] )->name( "index" );

    Route::post( "upload", [
        Import\PackingFormHandingController::class,
        "upload"
    ] )->name( "upload" );
    Route::get( "show", [
        Import\PackingFormHandingController::class,
        "show"
    ] )->name( "show" );
    Route::get( "update/{print_label}", [
        Import\PackingFormHandingController::class,
        "update"
    ] )->name( "update" );

} );

Route::middleware( [ 'url_check:import.leave_reminder.index' ] )->name( "leave_reminder." )->prefix( "/leave_reminder" )->group( function () {

    Route::get( "index", [
        Import\LeaveReminderController::class,
        "index"
    ] )->name( "index" );

    Route::post( "upload", [
        Import\LeaveReminderController::class,
        "upload"
    ] )->name( "upload" );
    Route::get( "show", [
        Import\LeaveReminderController::class,
        "show"
    ] )->name( "show" );
    Route::post( "update", [
        Import\LeaveReminderController::class,
        "update"
    ] )->name( "update" );
    Route::get( "get_sampling_file", [
        Import\LeaveReminderController::class,
        "get_sampling_file"
    ] )->name( "get_sampling_file" );

} );
