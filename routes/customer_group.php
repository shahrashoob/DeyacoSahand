<?php

use App\Http\Controllers\Customer;

//
Route::prefix( 'buy' )->name( "buy." )->group( function () {

    Route::match( [ 'get', 'post' ], "index/{order}", [ Customer\BuyController::class, "index" ] )->name( "index" );
    Route::match( [ 'get', 'post' ], "index_property/{order}/{goods_kind}", [
        Customer\BuyController::class,
        "index_property"
    ] )->name( "index_property" );
    Route::match( [ 'get', 'post' ], "index_product/{order}/{goods_kind_property_value}", [
        Customer\BuyController::class,
        "index_product"
    ] )->name( "index_product" );


    Route::get( "new_order", [ Customer\BuyController::class, "new_order" ] )->name( "new_order" );
    Route::get( "edit_order/{order}", [ Customer\BuyController::class, "edit_order" ] )->name( "edit_order" );


    Route::get( "complete_order/{customer}", [
        Customer\BuyController::class,
        "complete_order"
    ] )->name( "complete_order" );

    Route::get( "list", [ Customer\BuyController::class, "list" ] )->name( "list" );
    Route::post( "add_to_shopping_cart/{order}/{customer}/{product}", [
        Customer\BuyController::class,
        "add_to_shopping_cart"
    ] )->name( "add_to_shopping_cart" );

    Route::get( "remove_from_shopping_cart/{order}/{customer}/{product}", [
        Customer\BuyController::class,
        "remove_from_shopping_cart"
    ] )->name( "remove_from_shopping_cart" );

    Route::get( "show_shopping_product/{order}/{customer}/{product}", [
        Customer\BuyController::class,
        "show_shopping_product"
    ] )->name( "show_shopping_product" );
    Route::post( "store", [ Customer\BuyController::class, "store" ] )->name( "store" );

    Route::get( "shopping_cart/{order}/{selling_type_id?}", [
        Customer\BuyController::class,
        "shopping_cart"
    ] )->name( "shopping_cart" );
    Route::get( "address/{order}", [ Customer\BuyController::class, "address" ] )->name( "address" );
    Route::post( "address_submit/{order}", [
        Customer\BuyController::class,
        "address_submit"
    ] )->name( "address_submit" );
    Route::get( "address_edit/{order}/{address}", [
        Customer\BuyController::class,
        "address_edit"
    ] )->name( "address_edit" );
    Route::post( "address_edit_submit/{order}/{address}", [
        Customer\BuyController::class,
        "address_edit_submit"
    ] )->name( "address_edit_submit" );
    Route::post( "shopping_cart_submit/{order}", [
        Customer\BuyController::class,
        "shopping_cart_submit"
    ] )->name( "shopping_cart_submit" );
    Route::get( "payment_method_step1/{order}", [
        Customer\BuyController::class,
        "payment_method_step1"
    ] )->name( "payment_method_step1" );
    Route::post( "payment_method_step1_submit/{order}", [
        Customer\BuyController::class,
        "payment_method_step1_submit"
    ] )->name( "payment_method_step1_submit" );


    Route::get( "payment_method_step2/{order}", [
        Customer\BuyController::class,
        "payment_method_step2"
    ] )->name( "payment_method_step2" );
    Route::post( "payment_method_step2_submit/{order}", [
        Customer\BuyController::class,
        "payment_method_step2_submit"
    ] )->name( "payment_method_step2_submit" );

    Route::post( "shopping_cart_confirm/{order}", [
        Customer\BuyController::class,
        "shopping_cart_confirm"
    ] )->name( "shopping_cart_confirm" );
    Route::get( "shopping_cart_submit_result/{order}", [
        Customer\BuyController::class,
        "shopping_cart_submit_result"
    ] )->name( "shopping_cart_submit_result" );
    Route::post( "change_selling_type/{order}", [
        Customer\BuyController::class,
        "change_selling_type"
    ] )->name( "change_selling_type" );

    Route::prefix('order_packing_form')->name("order_packing_form.")->group(function () {
        Route::get("index/{order}/{orderConsumedProduct_id}", [Customer\OrderPackingFormController::class, "index"])->name("index");
        Route::get("delete_general_item/{order}/{orderConsumedProduct}", [Customer\OrderPackingFormController::class, "delete_general_item"])->name("delete_general_item");
        Route::post("submit_new_packing_form/{order}/{order_consumed_product}", [Customer\OrderPackingFormController::class, "submit_new_packing_form"])->name("submit_new_packing_form");
        Route::get("delete_order_packing_form/{order}/{order_packing_form}", [Customer\OrderPackingFormController::class, "delete_order_packing_form"])->name("delete_order_packing_form");
    });

    Route::get( "reserve_amount/{product}/{order}", [
        Customer\BuyController::class,
        "reserve_amount"
    ] )->name( "reserve_amount" );
    Route::get( "product_request_form_amount/{product}/{order}", [
        Customer\BuyController::class,
        "product_request_form_amount"
    ] )->name( "product_request_form_amount" );
} );

Route::prefix( 'order' )->name( "order." )->group( function () {

    Route::match( [ 'get', 'post' ], "index", [
        Customer\OrderController::class,
        "index"
    ] )->name( "index" );

    Route::get( "show/{order}", [ Customer\OrderController::class, "show" ] )->name( "show" );
    Route::post( "confirm/{order}", [ Customer\OrderController::class, "confirm" ] )->name( "confirm" );
    Route::get( "reject/{order}", [ Customer\OrderController::class, "reject" ] )->name( "reject" );
    Route::get( "log/{order}", [ Customer\OrderController::class, "log" ] )->name( "log" );
    Route::get( "view_form/{order}/{form}", [
        Customer\OrderController::class,
        "view_form"
    ] )->name( "view_form" );
    Route::get( "view_product_request_form/{order}/{product_request_form}", [
        Customer\OrderController::class,
        "view_product_request_form"
    ] )->name( "view_product_request_form" );
    Route::get( "view_reject_product_form/{order}/{reject_product_form}", [
        Customer\OrderController::class,
        "view_reject_product_form"
    ] )->name( "view_reject_product_form" );

    Route::get("download_form/{order}/{form}/{packing_type_label_printing_type}/{print_type?}", [Customer\OrderController::class, "download_form"])->name("download_form");


    Route::prefix( 'reject_product' )->name( "reject_product." )->group( function () {

        Route::get( "index/{order}/{form}", [ Customer\RejectProductController::class, "index" ] )->name( "index" );
        Route::match( [ 'get', 'post' ], "step1/{order}/{form}", [
            Customer\RejectProductController::class,
            "step1"
        ] )->name( "step1" );
        Route::post( "confirm/{order}/{form}", [
            Customer\RejectProductController::class,
            "confirm"
        ] )->name( "confirm" );
        Route::post( "submit_send_product/{order}/{reject_product_form}", [
            Customer\RejectProductController::class,
            "submit_send_product"
        ] )->name( "submit_send_product" );
        Route::get( "download_form/{order}/{reject_product_form}", [
            Customer\RejectProductController::class,
            "download_form"
        ] )->name( "download_form" );

    } );

    Route::prefix( 'sending_material' )->name( "sending_material." )->group( function () {

        Route::get( "index/{order}/{product}", [ Customer\SendingMaterialController::class, "index" ] )->name( "index" );
        Route::get( "show_allocation/{order}/{machine_allocation}", [ Customer\SendingMaterialController::class, "show_allocation" ] )->name( "show_allocation" );

    } );

} );

Route::prefix( 'confirmation_of_receipt_of_product' )->name( "confirmation_of_receipt_of_product." )->group( function () {

    Route::get( "index/{order}/{form}", [
        Customer\ConfirmationOrReceiptOfProductController::class,
        "index"
    ] )->name( "index" );
    Route::post( "confirm_exist_form/{order}/{form}", [
        Customer\ConfirmationOrReceiptOfProductController::class,
        "confirm_exist_form"
    ] )->name( "confirm_exist_form" );

} );

Route::get( "print/factor/{order}", [ Customer\PrintController::class, "factor" ] )->name( "print.factor" );


Route::prefix( 'definition/' )->name( "definition." )
     ->group( function () {


         Route::middleware( [ 'url_check:customer_group.definition.admin.index' ] )->prefix( 'admin/' )->name( "admin." )
              ->group( function () {

                  Route::match( [ 'get', 'post' ], "index", [
                      Customer\Definition\AdminController::class,
                      "index"
                  ] )->name( "index" );
                  Route::get( "create", [ Customer\Definition\AdminController::class, "create" ] )->name( "create" );
                  Route::post( "store", [ Customer\Definition\AdminController::class, "store" ] )->name( "store" );
                  Route::get( "edit/{customer}", [
                      Customer\Definition\AdminController::class,
                      "edit"
                  ] )->name( "edit" );
                  Route::post( "update/{customer}", [
                      Customer\Definition\AdminController::class,
                      "update"
                  ] )->name( "update" );
                  Route::get( "edit_software_system/{customer}", [Customer\Definition\AdminController::class, "edit_software_system"] )->name( "edit_software_system" );
                  Route::post( "update_software_system/{customer}", [Customer\Definition\AdminController::class, "update_software_system"] )->name( "update_software_system" );
              } );

         Route::prefix( 'basic_information' )->name( "basic_information." )->group( function () {

             Route::get( "index/{customer}", [
                 Customer\Definition\BasicInformationController::class,
                 "index"
             ] )->name( "index" );

             Route::post( "submit/{customer}", [
                 Customer\Definition\BasicInformationController::class,
                 "submit"
             ] )->name( "submit" );
         } );

         Route::prefix( 'default' )->name( "default." )->group( function () {

             Route::get( "index", [
                 Customer\Definition\DefaultController::class,
                 "index"
             ] )->name( "index" );

             Route::post( "submit", [
                 Customer\Definition\DefaultController::class,
                 "submit"
             ] )->name( "submit" );
         } );
     } );


Route::prefix( 'tmp/' )->name( "tmp." )
     ->group( function () {

         Route::prefix( 'definition_customer/' )->name( "definition_customer." )
              ->group( function () {

                  Route::get( "index", [
                      Customer\TMP\DefinitionCustomerController::class,
                      "index"
                  ] )->name( "index" );
                  Route::get( "create", [ Customer\TMP\DefinitionCustomerController::class, "create" ] )->name( "create" );
                  Route::post( "store", [ Customer\TMP\DefinitionCustomerController::class, "store" ] )->name( "store" );
                  Route::get( "edit", [
                      Customer\TMP\DefinitionCustomerController::class,
                      "edit"
                  ] )->name( "edit" );
                  Route::post( "update", [
                      Customer\TMP\DefinitionCustomerController::class,
                      "update"
                  ] )->name( "update" );
              } );

         Route::prefix( 'product_creation' )->name( "product_creation." )->group( function () {

             Route::get( "index", [
                 Customer\TMP\ProductCreationController::class,
                 "index"
             ] )->name( "index" );

             Route::get( "create", [
                 Customer\TMP\ProductCreationController::class,
                 "create"
             ] )->name( "create" );

             Route::post( "submit", [
                 Customer\TMP\ProductCreationController::class,
                 "submit"
             ] )->name( "submit" );

             Route::get( "view/{product_creation_process}", [
                 Customer\TMP\ProductCreationController::class,
                 "view"
             ] )->name( "view" );


             Route::get( "log/{product_creation_process}", [
                 Customer\TMP\ProductCreationController::class,
                 "log"
             ] )->name( "log" );

             Route::get( "download/{product_creation_process}", [
                 Customer\TMP\ProductCreationController::class,
                 "download"
             ] )->name( "download" );

             Route::post( "submit_post_tracking_code/{product_creation_process}", [
                 Customer\TMP\ProductCreationController::class,
                 "submit_post_tracking_code"
             ] )->name( "submit_post_tracking_code" );

         } );
     } );


Route::prefix( 'retail_customer' )->name( "retail_customer." )->group( function () {

    Route::prefix( 'buy' )->name( "buy." )->group( function () {

        Route::get("index", [
            Customer\RetailCustomer\BuyController::class,
            "index"
        ])->name("index");

        Route::get("add_to_shopping_cart/{productTariff}", [
            Customer\RetailCustomer\BuyController::class,
            "add_to_shopping_cart"
        ])->name("add_to_shopping_cart");

        Route::get("shopping_cart/", [
            Customer\RetailCustomer\BuyController::class,
            "shopping_cart"
        ])->name("shopping_cart");

        Route::post("shopping_cart_submit/{order}", [
            Customer\RetailCustomer\BuyController::class,
            "shopping_cart_submit"
        ])->name("shopping_cart_submit");

        Route::get("bank/{order}", [
            Customer\RetailCustomer\BuyController::class,
            "bank"
        ])->name("bank");
        Route::post("submit_bank/{order}", [
            Customer\RetailCustomer\BuyController::class,
            "submit_bank"
        ])->name("submit_bank");
    });

    Route::prefix( 'order_list' )->name( "order_list." )->group( function () {

        Route::get("index", [
            Customer\RetailCustomer\OrderListController::class,
            "index"
        ])->name("index");

        Route::get("view/{order}", [
            Customer\RetailCustomer\OrderListController::class,
            "view"
        ])->name("view");
    });


} );
