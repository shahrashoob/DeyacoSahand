<?php


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Utility\OptionController;
use App\Http\Controllers\LineProductStation\Product;
use App\Http\Controllers\Utility\SmartObjectsController;
use App\Http\Controllers\HR\Api;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get("api_test", [\App\Http\Controllers\SampleController::class, "api_test"]);

Route::name("option.")->prefix("/option")->group(function () {
    Route::post("get", [OptionController::class, "get"])->name("get");
    Route::get("get_degree", [OptionController::class, "get_degree"])->name("get_degree");

});

Route::name("material_flow.")->prefix("material_flow/")->group(function () {
    Route::post("add_link/{product}/{bom}/{line_product_station}", [
        Product\MaterialFlowController::class,
        "add_link"
    ])->name("add_link");
});

Route::name("other.")->prefix("/other")->group(function () {
    Route::post("get_property_input_by_property_id", [
        OptionController::class,
        "get_property_input_by_property_id"
    ])->name("get_property_input_by_property_id");
    Route::post("add_packing_form_to_transport_item", [
        \App\Http\Controllers\Utility\Transport\DashboardController::class,
        "add_packing_form_to_transport_item"
    ])->name("add_packing_form_to_transport_item");
    Route::post("add_packing_form_to_transport_item_product_request_form", [
        \App\Http\Controllers\Warehouse\Transport\DashboardController::class,
        "add_packing_form_to_transport_item_product_request_form"
    ])->name("add_packing_form_to_transport_item_product_request_form");
    Route::post("warehouse_delivery_changeSelectedPacking", [
        \App\Http\Controllers\Warehouse\Out\DeliveryController::class,
        "warehouse_delivery_changeSelectedPacking"
    ])->name("warehouse_delivery_changeSelectedPacking");
    Route::post("add_packing_form_to_change_packing_quick_list", [
        \App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\ChangePackingQuickController::class,
        "add_packing_form_to_change_packing_quick_list"
    ])->name("add_packing_form_to_change_packing_quick_list");


    Route::post('complete_information_with_value/add_packing_form_api/{form_general_item}',
        [\App\Http\Controllers\Warehouse\Input\CompleteInformationWithValueController::class,
            'add_packing_form_api'])->name("complete_information_with_value.add_packing_form_api");

});


Route::middleware(['auth:sanctum'])->name("product_request_form.")->prefix("product_request_form")->group(function () {
    Route::post("get_exit_form_list_api/{product_request_form}/{page}", [
        App\Http\Controllers\Warehouse\Out\DashboardController::class,
        "get_exit_form_list_api"
    ])->name("get_exit_form_list_api");
    Route::post("get_prf_list_api/{product_request_form}/{page}", [
        App\Http\Controllers\Warehouse\Out\DashboardController::class,
        "get_prf_list_api"
    ])->name("get_prf_list_api");
    Route::post("get_index_rows_api", [
        App\Http\Controllers\Warehouse\Out\DashboardController::class,
        "get_index_rows_api"
    ])->name("get_index_rows_api");
});


Route::name("printer.")->middleware("throttle:1000000:1")->prefix("/printer")->group(function () {
    Route::get("getPrinterWidth/{printer_id}", [
        \App\Http\Controllers\Utility\Printer\PrinterAPIController::class,
        "getPrinterWidth"
    ])->name("getPrinterWidth");
    Route::get("getPrinterHeight/{printer_id}", [
        \App\Http\Controllers\Utility\Printer\PrinterAPIController::class,
        "getPrinterHeight"
    ])->name("getPrinterHeight");
    Route::get("getLatestId/{key}/{printer_id}", [
        \App\Http\Controllers\Utility\Printer\PrinterAPIController::class,
        "getLatestId"
    ])->name("getLatestId");
    Route::get("getPDFFile/{key}/{id}", [
        \App\Http\Controllers\Utility\Printer\PrinterAPIController::class,
        "getPDFFile"
    ])->name("getPDFFile");
    Route::get("PrintOK/{key}/{id}", [
        \App\Http\Controllers\Utility\Printer\PrinterAPIController::class,
        "PrintOK"
    ])->name("PrintOK");

});

Route::name("contractor.")->prefix("contractor")->withoutMiddleware("throttle:api")
    ->middleware("throttle:10000:1")->group(function () {
        Route::name("add_new_packing.")->prefix("add_new_packing")->group(function () {
            Route::post("add_new_product_to_packing_api",
                [
                    \App\Http\Controllers\Contractor\Panel\RegisterProductionController::class,
                    "add_new_product_to_packing_api"
                ])->
            name("add_new_product_to_packing_api");

            Route::post("add_new_lot_number_to_product_api",
                [
                    \App\Http\Controllers\Contractor\Panel\RegisterProductionController::class,
                    "add_new_lot_number_to_product_api"
                ])->
            name("add_new_lot_number_to_product_api");
        });
        Route::name("coordination_for_sending.")->prefix("coordination_for_sending")->group(function () {
            Route::post("get_production_info",
                [
                    \App\Http\Controllers\Contractor\Panel\CoordinationForSendingController::class,
                    "get_production_info"
                ])->
            name("get_production_info");
        });

        Route::name("admin.machine_allocation.")->prefix("admin/machine_allocation")->group(function () {
            Route::post("select_packing_form_api",
                [
                    \App\Http\Controllers\Contractor\Admin\ContractorAllocationController::class,
                    "select_packing_form_api"
                ])->
            name("select_packing_form_api");
        });
    });


Route::name("production/public_module/add_new_packing/")->middleware("throttle:10000000:1")->prefix("production/public_module/add_new_packing/")->group(function () {
    Route::post("add_new_product_to_packing_api",
        [
            \App\Http\Controllers\Production\PublicModule\RegisterProductionController::class,
            "add_new_product_to_packing_api"
        ])->
    name("add_new_product_to_packing_api");

    Route::post("add_new_lot_number_to_product_api",
        [
            \App\Http\Controllers\Production\PublicModule\RegisterProductionController::class,
            "add_new_lot_number_to_product_api"
        ])->
    name("add_new_lot_number_to_product_api");
});

Route::name("packing_form.")->prefix("packing_form")->group(function () {
    Route::name("quality_control.")->prefix("quality_control")->group(function () {
        Route::post("submit_change_api",
            [
                \App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingForm\QualityControlController::class,
                "submit_change_api"
            ])->
        name("submit_change_api");

    });
});
Route::name("warehouse.")->prefix("warehouse")->group(function () {
    Route::name("input.")->prefix("input")->group(function () {
        Route::post("submit_set_to_warehouse_api",
            [
                \App\Http\Controllers\Warehouse\DashboardController::class,
                "submit_set_to_warehouse_api"
            ])->
        name("submit_set_to_warehouse_api");
        Route::post("submit_new_packing_api",
            [
                \App\Http\Controllers\Warehouse\DashboardController::class,
                "submit_new_packing_api"
            ])->
        name("submit_new_packing_api");

        Route::post("entry_form/add_packing_api",
            [
                \App\Http\Controllers\Warehouse\Input\EntryFormController::class,
                "app_packing_api"
            ])->
        name("entry_form.add_packing_api");

    });
    Route::name("output.")->prefix("output")->group(function () {
        Route::post("exit_form_implementation/add_packing_api",
            [
                \App\Http\Controllers\Warehouse\Out\ExitFormImplementationController::class,
                "app_packing_api"
            ])->
        name("exit_form_implementation.add_packing_api");

        Route::post("exit_form_implementation2/add_packing_api",
            [
                \App\Http\Controllers\Warehouse\Out\ExitFormImplementation2Controller::class,
                "app_packing_api"
            ])->
        name("exit_form_implementation2.add_packing_api");

    });
    Route::name("warehouse_handling.")->prefix("warehouse_handling")->group(function () {
        Route::post("add_packing_form/add_packing_form_api",
            [
                \App\Http\Controllers\Warehouse\WarehouseHandling\AddPackingFromController::class,
                "add_packing_form_api"
            ])->
        name("add_packing_form.app_packing_form_api");

    });

    Route::post("add_remove_packing_form_pallet_api", [
        \App\Http\Controllers\Warehouse\Pallet\AddPackingFormController::class,
        "add_remove_packing_form_pallet_api"
    ])->name("add_remove_packing_form_pallet_api");

});

Route::name("smart_object.")->prefix("smart_object/")->group(function () {
    Route::post("update_contour_api",
        [
            SmartObjectsController::class,
            "updateContourAPI"
        ])->
    name("update_contour_api");

    Route::middleware(['auth:sanctum',])->name("passing_gate.")->prefix("passing_gate/")->group(function () {

        Route::middleware("throttle:10000000,1")->post("submit",
            [
                \App\Http\Controllers\HR\Personal\PassingGateApiController::class,
                "submit"
            ])->
        name("submit");

        Route::middleware("throttle:10000000,1")->post("system_info",
            [
                \App\Http\Controllers\HR\Personal\PassingGateApiController::class,
                "system_info"
            ])->
        name("system_info");
// ---------- IMAGE UPLOAD (TRAINING) ----------
        Route::post('/upload-image', [Api\ImageController::class, 'upload']);
        Route::post('/images/{id}/train', [Api\ImageController::class, 'markImageTrained']);
// ---------- PYTHON TRAINING IMAGES ----------
       Route::get('/python/images/{userId}', [Api\PythonController::class, 'getTrainingImages']);
       Route::get('/python/pp', [Api\PythonController::class, 'getAllUserIds']);
       Route::post('/train-daily', [Api\ImageController::class, 'trainDailyImages']);

// ---------- LIVE FACE FRAME ----------
        Route::post('/upload_face', [Api\FaceController::class, 'uploadFace']);
 });


    Route::post("login", [\App\Http\Controllers\Api\LoginController::class, "login"])->name("login");

    Route::middleware(['auth:sanctum'])->post("logout",
        [
            \App\Http\Controllers\Api\LoginController::class,
            "logout"
        ])->
    name("logout");


});


Route::match(['get', 'post'], "login", [\App\Http\Controllers\Api\LoginController::class, "login"])->name("login_api");

Route::middleware(['auth:sanctum'])->post("logout",
    [
        \App\Http\Controllers\Api\LoginController::class,
        "logout"
    ])->
name("logout_api");


Route::name("load_registration.control_packing_api")->prefix("utility/transport/loading/load_registration/")->group(function () {
    Route::post("control_packing_api",
        [
            \App\Http\Controllers\Utility\Transport\Loading\LoadRegistrationController::class,
            "control_packing_api"
        ])->
    name("update_contour_api");


});


Route::name("goods_kind_process.general.machine")->prefix("goods_kind_process/general/machine/")->group(function () {

    Route::post("add_change_grade_api",
        [
            \App\Http\Controllers\GoodsKindProcess\General\Machine\GeneralMaterialReturnToWarehouseController::class,
            "add_change_grade_api"
        ])->
    name("add_change_grade_api");


});
Route::name("accounting.client.buy.")->prefix("accounting/client/buy/")->group(function () {

    Route::get("verify_payment_api",
        [
            \App\Http\Controllers\Accounting\Client\BuyController::class,
            "verify_payment_api"
        ])->
    name("verify_payment_api");


});

Route::middleware(['auth:sanctum',])->name("order_api.")->prefix("order_api/")->group(function () {

    Route::middleware("throttle:500,1")->post("get_desktop_info_api",
        [
            \App\Http\Controllers\Sales\APIController::class,
            "get_desktop_info_api"
        ])->
    name("get_desktop_info_api");


});
