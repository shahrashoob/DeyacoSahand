<?php

use App\Http\Controllers\QualityControl;

Route::middleware( [ 'url_check:quality_control.dashboard.index' ] )->group( function () {

    Route::name( "dashboard." )->prefix( "/dashboard" )->group( function () {

        Route::match( [ 'get', 'post' ], "index", [
            QualityControl\DashboardController::class,
            "index"
        ] )->name( "index" );

        Route::get( "view_reject_product_form/{reject_product_form}", [
            QualityControl\DashboardController::class,
            "view_reject_product_form"
        ] )->name( "view_reject_product_form" );

        Route::get( "view_input_form/{form}", [
            QualityControl\DashboardController::class,
            "view_input_form"
        ] )->name( "view_input_form" );

        Route::get( "view_output_form/{form}", [
            QualityControl\DashboardController::class,
            "view_output_form"
        ] )->name( "view_output_form" );

    } );

    Route::name( "reject_product.confirm_quality." )->prefix( "/reject_product/confirm_quality" )->group( function () {

        Route::get( "confirm_reject_product_form/{reject_product_form}", [
            QualityControl\RejectProduct\ConfirmQualityController::class,
            "confirm_reject_product_form"
        ] )->name( "confirm_reject_product_form" );

    } );
    Route::name( "reject_product.confirm_sale_expert." )->prefix( "/reject_product/confirm_sale_expert" )->group( function () {

        Route::get( "confirm_reject_product_form/{reject_product_form}", [
            QualityControl\RejectProduct\ConfirmSaleExpertController::class,
            "confirm_reject_product_form"
        ] )->name( "confirm_reject_product_form" );

    } );

    Route::name( "input_form.confirm_quality." )->prefix( "/input_form/confirm_quality" )->group( function () {

        Route::post( "confirm_input_form/{form}", [
            QualityControl\InputForm\ConfirmQualityController::class,
            "confirm_input_form"
        ] )->name( "confirm_input_form" );

        Route::get( "reject_input_form/{form}", [
            QualityControl\InputForm\ConfirmQualityController::class,
            "reject_input_form"
        ] )->name( "reject_input_form" );

    } );

});
