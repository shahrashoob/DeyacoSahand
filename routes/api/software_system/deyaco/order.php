<?php

use App\Http\Controllers\Utility\SoftwareSystem;


        Route::post( "call_order_registration", [SoftwareSystem\Deyaco\Order\OrderApiController::class, "call_order_registration"] )->name( "call_order_registration" );
        Route::post( "call_coordination_for_sending", [SoftwareSystem\Deyaco\Order\OrderApiController::class, "call_coordination_for_sending"] )->name( "call_coordination_for_sending" );
        Route::post( "add_input_form_for_contractor", [SoftwareSystem\Deyaco\Order\OrderApiController::class, "add_input_form_for_contractor"] )->name( "add_input_form_for_contractor" );
        Route::post( "add_input_form_for_customer", [SoftwareSystem\Deyaco\Order\OrderApiController::class, "add_input_form_for_customer"] )->name( "add_input_form_for_customer" );
        Route::post( "confirm_applicant_for_input_form", [SoftwareSystem\Deyaco\Order\OrderApiController::class, "confirm_applicant_for_input_form"] )->name( "confirm_applicant_for_input_form" );
        Route::post( "register_packing_in_to_system_software", [SoftwareSystem\Deyaco\Order\OrderApiController::class, "register_packing_in_to_system_software"] )->name( "register_packing_in_to_system_software" );

