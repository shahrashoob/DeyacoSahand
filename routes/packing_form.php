<?php
//
//use App\Http\Controllers\PackingForm;
//
//Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {
//    Route::match( [ 'get', 'post' ], "index", [
//        PackingForm\DashboardController::class,
//        "index"
//    ] )->name( "index" );
//    Route::get( "view/{packing_form}", [
//        PackingForm\DashboardController::class,
//        "view"
//    ] )->name( "view" );
//    Route::get( "qr/{packing_form}", [
//        PackingForm\DashboardController::class,
//        "qr"
//    ] )->name( "qr" );
//
//    Route::get( "delivery_to_warehouse/{packing_form}", [
//        PackingForm\DashboardController::class,
//        "delivery_to_warehouse"
//    ] )->name( "delivery_to_warehouse" );
//} );
//
//
//Route::name( "delivery_to_warehouse." )->prefix( "/delivery_to_warehouse" )->group( function () {
//    Route::get( "get_nosa_code/{packing_form}", [
//        PackingForm\DeliveryToWarehouseController::class,
//        "get_nosa_code"
//    ] )->name( "get_nosa_code" );
//    Route::post( "submit_nosa_code/{packing_form}", [
//        PackingForm\DeliveryToWarehouseController::class,
//        "submit_nosa_code"
//    ] )->name( "submit_nosa_code" );
//    Route::post( "submit/{packing_form}", [
//        PackingForm\DeliveryToWarehouseController::class,
//        "submit"
//    ] )->name( "submit" );
//} );
//
//Route::name( "print_qr." )->prefix( "/print_qr" )->group( function () {
//    Route::get( "index/{packing_form}/{back_url_route?}/{id?}", [
//        PackingForm\PrintQRController::class,
//        "index"
//    ] )->name( "index" );
//    Route::post( "submit/{packing_form}", [
//        PackingForm\PrintQRController::class,
//        "submit"
//    ] )->name( "submit" );
//    Route::get( "download/{packing_form}", [
//        PackingForm\PrintQRController::class,
//        "download"
//    ] )->name( "download" );
//} );
//Route::name( "change_packing." )->prefix( "/change_packing" )->group( function () {
//    Route::get( "index/{packing_form}", [
//        PackingForm\ChangePackingController::class,
//        "index"
//    ] )->name( "index" );
//
//    Route::post( "submit/{packing_form}", [
//        PackingForm\ChangePackingController::class,
//        "submit"
//    ] )->name( "submit" );
//
//    Route::post( "confirm/{packing_form}", [
//        PackingForm\ChangePackingController::class,
//        "confirm"
//    ] )->name( "confirm" );
//
//    Route::get( "section/{packing_form}/{band_code}", [
//        PackingForm\ChangePackingController::class,
//        "section"
//    ] )->name( "section" );
//    Route::post( "submit_section/{packing_form}/{band_code}", [
//        PackingForm\ChangePackingController::class,
//        "submit_section"
//    ] )->name( "submit_section" );
//    Route::post( "end_of_section/{packing_form}", [
//        PackingForm\ChangePackingController::class,
//        "end_of_section"
//    ] )->name( "end_of_section" );
//    Route::get( "delete_section/{packing_form}/{FabricRaw_grading}", [
//        PackingForm\ChangePackingController::class,
//        "delete_section"
//    ] )->name( "delete_section" );
//} );
//Route::name( "change_in_warehouse." )->prefix( "/change_in_warehouse" )->group( function () {
//    Route::get( "index/{packing_form}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "index"
//    ] )->name( "index" );
//
//    Route::post( "submit/{packing_form}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "submit"
//    ] )->name( "submit" );
//
//    Route::post( "confirm/{packing_form}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "confirm"
//    ] )->name( "confirm" );
//
//    Route::get( "section/{packing_form}/{band_code}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "section"
//    ] )->name( "section" );
//    Route::post( "submit_section/{packing_form}/{band_code}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "submit_section"
//    ] )->name( "submit_section" );
//    Route::post( "end_of_section/{packing_form}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "end_of_section"
//    ] )->name( "end_of_section" );
//    Route::get( "delete_section/{packing_form}/{FabricRaw_grading}", [
//        PackingForm\ChangeInWarehouseController::class,
//        "delete_section"
//    ] )->name( "delete_section" );
//} );
//
