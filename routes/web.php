<?php

use App\Http\Controllers\Accounting\Offer\OfferController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Production\ProductionCardController;
use App\Http\Controllers\Purchase\PurchaseController;
use App\Http\Controllers\SampleController;
use App\Http\Controllers\Utility\Transport;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    $website = env("WEBSITE_TEMPLATE");
    if ($website) {
        return redirect("index");
    }
    return redirect("/login");
});

Route::get("index", function () {

    $website = env("WEBSITE_TEMPLATE");
    if ($website) {
        return view("website.$website.index");
    }
    return redirect("/login");
});

Route::get('language/{locale}', function ($locale) {

    if (in_array($locale, array_keys(config("app.locales")))) {
        app()->setLocale($locale);
        session()->put('locale', $locale);

        return redirect()->back();
    }

    return redirect()->back()->withErrors("سامانه از زبان انتخاب شده پشتیبانی نمی کند.");
})->name("language");

//Route::get('device_info/{mac_address}', [DashboardController::class, "device_info"])->name("device_info");


Route::get("/catalog_index", [SampleController::class, "catalog_index"])->name("catalog_index");
Route::post("/catalog_submit", [SampleController::class, "catalog_submit"])->name("catalog_submit");

Route::get("/qr_link/{key}", [DashboardController::class, "qr_link"])->name("qr_link");
Route::post("/login_sms", [DashboardController::class, "login_sms"])->name("login_sms");
Route::get("/login_token", [DashboardController::class, "login_token"])->name("login_token");
Route::post("/submit_token", [DashboardController::class, "submit_token"])->name("submit_token");


Route::get("/verify_payment", [\App\Http\Controllers\Accounting\Client\BuyController::class, "verify_payment"])->name("verify_payment");


Route::middleware(['url_check:admin.user-activity'])->get("/sdfsdf/sdfsl/sdfl/234/user-activity", function () {

    return redirect("/admin/user-activity");
})->name("admin.user-activity");

Route::get("/page1", function () {
    return view("demo/page1");
})->name("page1");
Route::get("/page2", function () {
    return view("demo/page2");
});
Route::get("/page21", function () {
    return view("demo/page21");
})->name("page21");
Route::get("/page22", function () {
    return view("demo/page22");
});
Route::get("/page23", function () {
    return view("demo/page23");
})->name("page23");
Route::middleware(['auth:sanctum', 'verified'])
    ->group(function () {
        Route::get('/dashboard', [DashboardController::class, "home"])->name('dashboard');
        Route::get('/test/{x?}/{y?}/{z?}', [SampleController::class, "test"])->name('test')->middleware("throttle:20,1");
        Route::post('/submit_test/{x?}/{y?}/{z?}', [SampleController::class, "submit_test"])->name('submit_test')->middleware("throttle:20,1");

        Route::get('/update/{force?}', [DashboardController::class, "update"])->name('update');
        Route::get('/confirm_pup_up/{pup_up}', [
            DashboardController::class,
            "confirm_pup_up"
        ])->name('confirm_pup_up');
        Route::get('/change_pass', [DashboardController::class, "change_pass"])->name('change_pass');
        Route::post('/submit_change_pass', [
            DashboardController::class,
            "submit_change_pass"
        ])->name('submit_change_pass');


    });

Route::get('/reset_pass', [DashboardController::class, "reset_pass"])->name('reset_pass');
Route::post('/submit_reset_pass', [
    DashboardController::class,
    "submit_reset_pass"
])->name('submit_reset_pass');
Route::get('/add_caption_for_device/{worker}/{user_device}', [DashboardController::class, "add_caption_for_device"])->name('add_caption_for_device');
Route::post('/submit_add_caption_for_device/{worker}/{user_device}', [
    DashboardController::class,
    "submit_add_caption_for_device"
])->name('submit_add_caption_for_device');
Route::get('/reset_pass_verification_code', [
    DashboardController::class,
    "reset_pass_verification_code"
])->name('reset_pass_verification_code');
Route::post('/reset_pass_confirm_verification_code', [
    DashboardController::class,
    "reset_pass_confirm_verification_code"
])->name('reset_pass_confirm_verification_code');

//Route::middleware( [ 'auth:sanctum', 'verified' ] )
//     ->prefix( "asdl/i/esl/dkjfa/li/kproduction" )->name( "production." )->group( function () {
//   } );
//
# بازرگانی
Route::middleware(['auth:sanctum', 'verified'])->prefix('ael/qweus/baksu/efpur/chase')
    ->name("purchase.")->group(function () {

        Route::get("purchase/list/", [PurchaseController::class, "list"])->name("purchase.list");
        Route::get("purchase/view_details/{purchaseOrderProduct}", [
            PurchaseController::class,
            "view_details"
        ])->name("purchase.view_details");
        // Route::post("purchase/palet_sheet_post/",[WarehousePrintController::class,"palet_sheet_post"])->name("print.palet_sheet_post");

    });


//Route::get("/index/{step?}", [CallController::class, "index"])->name("call.index");
//Route::post("/create", [CallController::class, "create"])->name("call.create");
//Route::get("/rscript", [CallController::class, "rscript"])->name("call.rscript");
//Route::get("/test_r", [CallController::class, "test_r"])->name("call.test_r");
//Route::get("/cancel/{call}/{pass}", [CallController::class, "cancel"])->name("call.cancel");

//Route::get( "/index", [
//    MenuSettingController::class,
//    "index"
//] )->prefix( "lk/sdi/sa/kjs/sdf/df/s/ae/fdfsdfks/ldjf/menu" )
//     ->name( "menu.index" );

Route::middleware(['url_check:accounting.offer.index'])->middleware([
    'auth:sanctum',
    'verified'
])->prefix('asdf/liess/asdfad/lieljiaccounting')->name("accounting.")
    ->group(function () {
        Route::get("accounting/offer/index", [OfferController::class, "index"])->name("offer.index");
        Route::get("accounting/offer/create/{type}", [OfferController::class, "create"])->name("offer.create");
        Route::post("accounting/offer/store", [OfferController::class, "store"])->name("offer.store");
        Route::get("accounting/offer/de_active/{offer}", [
            OfferController::class,
            "de_active"
        ])->name("offer.de_active");

    });


Route::get("sample/verta", [SampleController::class, "verta"]);

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("Personal/{worker}/{key}/{timestamp?}", [
    App\Http\Controllers\HR\PersonalController::class,
    "index_qr"
])->name("Personal");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCLP/{transport_item}/{key}", [Transport\DashboardController::class, "DCLP_QR"])->name("DCLP_QR");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCBL/{transport}/{key}", [Transport\DashboardController::class, "DCBL_QR"])->name("DCBL_QR");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCRP/{product_request_form}", [App\Http\Controllers\Warehouse\Out\DashboardController::class, "DCRP_QR"])->name("DCRP_QR");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCPK/{packing_form}/{key}", [
    App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingFormController::class,
    "DCPK_QR"
])->name("DCPK_QR");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCSC/{allocation}/{key}", [
    App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingFormController::class,
    "DCSC_QR" // Sample Control
])->name("DCSC_QR");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCPrFr/{production_form}/{key}", [
    App\Http\Controllers\GoodsKindProcess\FabricRaw\PackingFormController::class,
    "DCPrFr_QR" // Production Form
])->name("DCPrFr_QR");


Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCEF/{form}/{key}/{route_back?}", [
    App\Http\Controllers\Warehouse\Out\ExitFormController::class,
    "DCEF_QR"
])->name("DCEF_QR");

Route::post("submit_QR/{form}", [
    App\Http\Controllers\Warehouse\Out\ExitFormController::class,
    "submit_QR"
])->name("wh.out.exit_form.submit_QR");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCPR/{product}", [
    App\Http\Controllers\LineProductStation\ProductController::class,
    "DCPR_QR"
])->name("DCPR_QR");


Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCSL/{product}", [
    App\Http\Controllers\Utility\SpecialLicense\Panel\DashboardController::class,
    "DCSL_QR"
])->name("DCSL_QR");


Route::get("Report/{report_id}", function ($report_id) {
    return redirect()->route("report." . $report_id . ".index");
});

// Order Customer
Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCOF/{order_code}", [
    App\Http\Controllers\Customer\OrderController::class,
    "DCOF_SortLink"
])->name("DCOF_SortLink");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCOV/{order_code}", [ // order view
    App\Http\Controllers\Sales\DashboardController::class,
    "DCOV_SortLink"
])->name("DCOV_SortLink");

// cross_sectional_management
Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCCSM", [
    App\Http\Controllers\Report\CrossSectionalManagment\DashboardController::class,
    "DCCSM_SortLink"
])->name("DCCSM_SortLink");
Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCRG/{reject_product_form}/{key}", [
    App\Http\Controllers\Warehouse\Reject\RejectProductFormController::class,
    "DCRG_SortLink"
])->name("DCRG_SortLink");
Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCEM/{key}", [
    App\Http\Controllers\HR\Employment\Register\StartController::class,
    "link"
])->name("DCEM_SortLink");
Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("Machine/{machine}/{code}", [
    App\Http\Controllers\Production\MachineController::class,
    "Machine_ShortLink"
])->name("Machine_ShortLink");
Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCWT/{office_automation_work}/{key}/{company?}", [
    App\Http\Controllers\Utility\OfficeAutomation\DashboardController::class,
    "DCWT_ShortLink"
])->name("DCWT_ShortLink");
Route::middleware([
    'web'
])->get("BarcodeLink_QR/{code}", [
    DashboardController::class,
    "qr_link"
])->name("BarcodeLink_QR");

Route::middleware([
    'web'
])->get("employment/register/{key?}", [
    \App\Http\Controllers\HR\Employment\Register\StartController::class, "index",
])->name("Register_ShortLink");

Route::middleware([
    'web',
])->get("DCESLink/{key}", [
    App\Http\Controllers\HR\Employment\Register\StartController::class, "link"
])->name("DCESLink");

Route::middleware([
    'web',
])->get("DCRE_ShortLink/{reservoir}/{key}", [
    App\Http\Controllers\LineProductStation\Reservoir\DashboardController::class, "DCRE_ShortLink"
])->name("DCRE_ShortLink");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCWS/{key}", [
    App\Http\Controllers\Warehouse\WarehouseShelving\DashboardController::class,
    "DCWS_ShortLink"
])->name("DCWS_ShortLink");

Route::middleware([
    'web',
    'auth:sanctum',
    'verified'
])->get("DCPC/{pallet}/{random}", [
    App\Http\Controllers\Warehouse\Pallet\PrintPalletController::class,
    "DCPC_QR"
])->name("DCPC_QR"); // Pallet Count


Route::get("/logout/{route?}", function (Request $request, $route = "login") {
    $error = session("error_login") ?? null;
    $logout_data = session("logout_data") ?? null;
    \Auth::logout();
    $request->session()->flush();


    session(["logout_data" => $logout_data]);
    $website = env("WEBSITE_TEMPLATE");
    if ($website) {
        return redirect("index")->withErrors($error);
    }

    return redirect()->route($route, "login")->withErrors($error);
})->name("logout");

/******* Public Route in Employment ***************/
Route::prefix('hr/employment/register')->name("hr.employment.register.")->group(function () {

    Route::name("agent.")->prefix("agent")->group(function () {
        Route::name("confirm_agent.")->prefix("confirm_agent")->group(function () {
            Route::get("index/{agent}/{active_code}", [\App\Http\Controllers\HR\Employment\Register\Agent\ConfirmAgentController::class, "index"])->name("index");
            Route::post("submit/{agent}/{active_code}", [\App\Http\Controllers\HR\Employment\Register\Agent\ConfirmAgentController::class, "submit"])->name("submit");
            Route::get("reply/{agent}/{active_code}", [\App\Http\Controllers\HR\Employment\Register\Agent\ConfirmAgentController::class, "reply"])->name("reply");
        });
    });
    Route::name("contractor.")->prefix("contractor")->group(function () {
        Route::name("confirm_drafting_contract.")->prefix("confirm_drafting_contract")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Contractor\ConfirmDraftingContractController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Contractor\ConfirmDraftingContractController::class, "submit"])->name("submit");
            Route::get("print/{key?}", [\App\Http\Controllers\HR\Employment\Register\Contractor\ConfirmDraftingContractController::class, "print"])->name("print");
        });
        Route::name("agent.")->prefix("agent")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Contractor\AgentController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Contractor\AgentController::class, "submit"])->name("submit");
            Route::get("destroy/{key?}/{agent}", [\App\Http\Controllers\HR\Employment\Register\Contractor\AgentController::class, "destroy"])->name("destroy");
            Route::get("reply/{key?}/{agent}", [\App\Http\Controllers\HR\Employment\Register\Contractor\AgentController::class, "reply"])->name("reply");

        });
    });

    Route::name("customer.")->prefix("customer")->group(function () {
        Route::name("confirm_drafting_contract.")->prefix("confirm_drafting_contract")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Customer\ConfirmDraftingContractController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Customer\ConfirmDraftingContractController::class, "submit"])->name("submit");
            Route::get("print/{key?}", [\App\Http\Controllers\HR\Employment\Register\Customer\ConfirmDraftingContractController::class, "print"])->name("print");
        });
        Route::name("agent.")->prefix("agent")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Customer\AgentController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Customer\AgentController::class, "submit"])->name("submit");
            Route::get("destroy/{key?}/{agent}", [\App\Http\Controllers\HR\Employment\Register\Customer\AgentController::class, "destroy"])->name("destroy");
            Route::get("reply/{key?}/{agent}", [\App\Http\Controllers\HR\Employment\Register\Customer\AgentController::class, "reply"])->name("reply");

        });
    });

    Route::name("supplier.")->prefix("supplier")->group(function () {
        Route::name("confirm_drafting_contract.")->prefix("confirm_drafting_contract")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Supplier\ConfirmDraftingContractController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Supplier\ConfirmDraftingContractController::class, "submit"])->name("submit");
            Route::get("print/{key?}", [\App\Http\Controllers\HR\Employment\Register\Supplier\ConfirmDraftingContractController::class, "print"])->name("print");
        });
        Route::name("agent.")->prefix("agent")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Supplier\AgentController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Supplier\AgentController::class, "submit"])->name("submit");
            Route::get("destroy/{key?}/{agent}", [\App\Http\Controllers\HR\Employment\Register\Supplier\AgentController::class, "destroy"])->name("destroy");
            Route::get("reply/{key?}/{agent}", [\App\Http\Controllers\HR\Employment\Register\Supplier\AgentController::class, "reply"])->name("reply");

        });
    });

    Route::prefix('start')->name("start.")->group(function () {
        Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\StartController::class, "index"])->name("index");
        Route::get("link/{key?}", [\App\Http\Controllers\HR\Employment\Register\StartController::class, "link"])->name("link");
        Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\StartController::class, "submit"])->name("submit");
        Route::post("submit_link/{key?}", [\App\Http\Controllers\HR\Employment\Register\StartController::class, "submit_link"])->name("submit_link");
        Route::get("exist_national_code/{key?}", [\App\Http\Controllers\HR\Employment\Register\StartController::class, "exist_national_code"])->name("exist_national_code");
        Route::post("submit_exist_national_code/{key?}", [\App\Http\Controllers\HR\Employment\Register\StartController::class, "submit_exist_national_code"])->name("submit_exist_national_code");

    });

    Route::prefix('confirm_mobile')->name("confirm_mobile.")->group(function () {
        Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\ConfirmMobileController::class, "index"])->name("index");
        Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\ConfirmMobileController::class, "submit"])->name("submit");
    });
    Route::prefix('personal_info')->name("personal_info.")->group(function () {
        Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\PersonalInfoController::class, "index"])->name("index");
        Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\PersonalInfoController::class, "submit"])->name("submit");
        Route::get("upload/{key}", [\App\Http\Controllers\HR\Employment\Register\PersonalInfoController::class, "upload"])->name("upload");
        Route::post("submit_upload/{key}", [\App\Http\Controllers\HR\Employment\Register\PersonalInfoController::class, "submit_upload"])->name("submit_upload");
        Route::get("exist_national_code/{key}", [\App\Http\Controllers\HR\Employment\Register\PersonalInfoController::class, "exist_national_code"])->name("exist_national_code");
        Route::post("submit_exist_national_code/{key}", [\App\Http\Controllers\HR\Employment\Register\PersonalInfoController::class, "submit_exist_national_code"])->name("submit_exist_national_code");


    });
    Route::prefix('address')->name("address.")->group(function () {
        Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\AddressController::class, "index"])->name("index");
        Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\AddressController::class, "submit"])->name("submit");
        Route::get("upload/{key}", [\App\Http\Controllers\HR\Employment\Register\AddressController::class, "upload"])->name("upload");
        Route::post("submit_upload/{key}", [\App\Http\Controllers\HR\Employment\Register\AddressController::class, "submit_upload"])->name("submit_upload");
    });

    Route::prefix('other')->name("other.")->group(function () {
        Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\OtherController::class, "index"])->name("index");
        Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\OtherController::class, "submit"])->name("submit");
    });

    Route::prefix('confirm_information')->name("confirm_information.")->group(function () {
        Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\ConfirmInformationController::class, "index"])->name("index");
        Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\ConfirmInformationController::class, "submit"])->name("submit");

    });

    Route::name("personal.")->prefix("personal")->group(function () {
        Route::prefix('academic_degree')->name("academic_degree.")->group(function () {
            Route::get('/download/{employment}/{employment_document_type}', [\App\Http\Controllers\HR\Employment\Register\Personal\AcademicDegreeController::class, "download"])->name('download');
            Route::get("index/{key}/{academic_degree_type_id?}", [\App\Http\Controllers\HR\Employment\Register\Personal\AcademicDegreeController::class, "index"])->name("index");
            Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\AcademicDegreeController::class, "submit"])->name("submit");
            Route::post("submit_upload/{key}/{user_academic_degree}", [\App\Http\Controllers\HR\Employment\Register\Personal\AcademicDegreeController::class, "submit_upload"])->name("submit_upload");
            Route::get("upload/{key}/{user_academic_degree}", [\App\Http\Controllers\HR\Employment\Register\Personal\AcademicDegreeController::class, "upload"])->name("upload");
            Route::get("destroy/{key}/{user_academic_degree}", [\App\Http\Controllers\HR\Employment\Register\Personal\AcademicDegreeController::class, "destroy"])->name("destroy");

        });
        Route::prefix('job_information')->name("job_information.")->group(function () {
            Route::get('/download/{employment}/{employment_document_type}', [\App\Http\Controllers\HR\Employment\Register\Personal\JobInformationController::class, "download"])->name('download');
            Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\JobInformationController::class, "index"])->name("index");
            Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\JobInformationController::class, "submit"])->name("submit");
            Route::post("submit_upload/{key}/{user_job_information}", [\App\Http\Controllers\HR\Employment\Register\Personal\JobInformationController::class, "submit_upload"])->name("submit_upload");
            Route::get("upload/{key}/{user_job_information}", [\App\Http\Controllers\HR\Employment\Register\Personal\JobInformationController::class, "upload"])->name("upload");
            Route::get("destroy/{key}/{user_job_information}", [\App\Http\Controllers\HR\Employment\Register\Personal\JobInformationController::class, "destroy"])->name("destroy");

        });
        Route::prefix('educational_course')->name("educational_course.")->group(function () {
            Route::get('/download/{employment}/{employment_document_type}', [\App\Http\Controllers\HR\Employment\Register\Personal\EducationalCourseController::class, "download"])->name('download');
            Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\EducationalCourseController::class, "index"])->name("index");
            Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\EducationalCourseController::class, "submit"])->name("submit");
            Route::post("submit_upload/{key}/{user_educational_course}", [\App\Http\Controllers\HR\Employment\Register\Personal\EducationalCourseController::class, "submit_upload"])->name("submit_upload");
            Route::get("upload/{key}/{user_educational_course}", [\App\Http\Controllers\HR\Employment\Register\Personal\EducationalCourseController::class, "upload"])->name("upload");
            Route::get("destroy/{key}/{user_educational_course}", [\App\Http\Controllers\HR\Employment\Register\Personal\EducationalCourseController::class, "destroy"])->name("destroy");
        });
        Route::name("confirm_drafting_contract.")->prefix("confirm_drafting_contract")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\ConfirmDraftingContractController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\ConfirmDraftingContractController::class, "submit"])->name("submit");
            Route::get("print/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\ConfirmDraftingContractController::class, "print"])->name("print");
        });
        Route::name("work_medicine.")->prefix("work_medicine")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\WorkMedicineController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\WorkMedicineController::class, "submit"])->name("submit");
            Route::get("letter/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\WorkMedicineController::class, "letter"])->name("letter");
        });
        Route::name("bank_information.")->prefix("bank_information")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\BankInformationController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\BankInformationController::class, "submit"])->name("submit");
            Route::get("destroy/{key?}/{user_bank_account}", [\App\Http\Controllers\HR\Employment\Register\Personal\BankInformationController::class, "destroy"])->name("destroy");
        });
        Route::prefix('military_information')->name("military_information.")->group(function () {
            Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\MilitaryInformationContoller::class, "index"])->name("index");
            Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\MilitaryInformationContoller::class, "submit"])->name("submit");
        });
        Route::prefix('dependent')->name("dependent.")->group(function () {
            Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\DependentContoller::class, "index"])->name("index");
            Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\DependentContoller::class, "submit"])->name("submit");
            Route::get("destroy/{key}/{user_dependent}", [\App\Http\Controllers\HR\Employment\Register\Personal\DependentContoller::class, "destroy"])->name("destroy");
            Route::post("submit_upload/{key}/{user_dependent}", [\App\Http\Controllers\HR\Employment\Register\Personal\DependentContoller::class, "submit_upload"])->name("submit_upload");
            Route::get("upload/{key}/{user_dependent}", [\App\Http\Controllers\HR\Employment\Register\Personal\DependentContoller::class, "upload"])->name("upload");
            Route::get('/download/{employment}/{employment_document_type}', [\App\Http\Controllers\HR\Employment\Register\Personal\DependentContoller::class, "download"])->name('download');
        });
        Route::name("upload_final_document.")->prefix("upload_final_document")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\UploadFinalDocumentsController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\UploadFinalDocumentsController::class, "submit"])->name("submit");
        });
        Route::name("upload_primary_document.")->prefix("upload_primary_document")->group(function () {
            Route::get("index/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\UploadPrimaryDocumentController::class, "index"])->name("index");
            Route::post("submit/{key?}", [\App\Http\Controllers\HR\Employment\Register\Personal\UploadPrimaryDocumentController::class, "submit"])->name("submit");
        });
        Route::prefix('confirm_upload_document')->name("confirm_upload_document.")->group(function () {
            Route::get("index/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\ConfirmUploadDocumentController::class, "index"])->name("index");
            Route::post("submit/{key}", [\App\Http\Controllers\HR\Employment\Register\Personal\ConfirmUploadDocumentController::class, "submit"])->name("submit");


        });
    });
    Route::get("index", [\App\Http\Controllers\HR\Employment\RegisterController::class, "index"])->name("index");
    Route::post("submit", [\App\Http\Controllers\HR\Employment\RegisterController::class, "submit"])->name("submit");
    Route::get("create/{national_code}", [\App\Http\Controllers\HR\Employment\RegisterController::class, "create"])->name("create");
    Route::post("confirm", [\App\Http\Controllers\HR\Employment\RegisterController::class, "confirm"])->name("confirm");
    Route::post("submit_confirm", [\App\Http\Controllers\HR\Employment\RegisterController::class, "submit_confirm"])->name("submit_confirm");
    Route::get("show_confirm", [\App\Http\Controllers\HR\Employment\RegisterController::class, "show_confirm"])->name("show_confirm");
    Route::post("submit_confirm", [\App\Http\Controllers\HR\Employment\RegisterController::class, "submit_confirm"])->name("submit_confirm");
    Route::get("create_exist_national_code/{national_code}", [\App\Http\Controllers\HR\Employment\RegisterController::class, "create_exist_national_code"])->name("create_exist_national_code");
    Route::post("confirm_exist_national_code", [\App\Http\Controllers\HR\Employment\RegisterController::class, "confirm_exist_national_code"])->name("confirm_exist_national_code");

});

Route::get('test1', function () {
    return view('test1');
});
/** لیست روت های پیامک */
//Route::get( "/{company}/DCEF/{token}/{key}", function ( $company, $token, $key ) {
//    switch ( $company ) {
//        case "harir":
//            return redirect()->away( "http://109.125.144.51:8085/DCEF/" . $token . "/" . $key );
//        case "arman":
//            return redirect()->away( "http://109.125.144.51:8086/DCEF/" . $token . "/" . $key );
//    }
//} );
//Route::get( "/{company}/DCWT/{key}/{token}", function ( $company, $token, $key ) {
//    switch ( $company ) {
//        case "harir":
//            return redirect()->away( "http://109.125.144.51:8085/DCWT/" . $key . "/" . $token );
//        case "arman":
//            return redirect()->away( "http://109.125.144.51:8086/DCWT/" . $key . "/" . $token );
//    }
//} );
//Route::get( "/{company}/DCCSM", function ( $company, $token ) {
//switch ( $company ) {
//case "harir":
//return redirect()->away( "http://109.125.144.51:8085/DCCSM" );
//case "arman":
//return redirect()->away( "http://109.125.144.51:8086/DCCSM" );
//}
//} );
//Route::get( "/{company}/Report/{token}", function ( $company, $token ) {
//switch ( $company ) {
//case "harir":
//return redirect()->away( "http://109.125.144.51:8085/Report/" . $token );
//case "arman":
//return redirect()->away( "http://109.125.144.51:8086/Report/" . $token );
//}
//} );
//Route::get( "/{company}/DCRG/{token}/{key}", function ( $company, $token,$key ) {
//switch ( $company ) {
//case "harir":
//return redirect()->away( "http://109.125.144.51:8085/DCRG/" . $token."/".$key );
//case "arman":
//return redirect()->away( "http://109.125.144.51:8086/DCRG/" . $token."/".$key );
//}
//} );
//Route::get( "/{company}/DCOF/{token}", function ( $company, $token ) {
//switch ( $company ) {
//case "harir":
//return redirect()->away( "http://109.125.144.51:8085/DCOF/" . $token );
//case "arman":
//return redirect()->away( "http://109.125.144.51:8086/DCOF/" . $token );
//}
//} );
//Route::get( "/{company}/Personal/{token}/{token2}", function ( $company, $token ,$token2) {
//switch ( $company ) {
//case "harir":
//return redirect()->away( "http://109.125.144.51:8085/Personal/" . $token ."/".$token2);
//case "arman":
//return redirect()->away( "http://109.125.144.51:8086/Personal/" . $token ."/".$token2 );
//}
//} );

//Route::get( "/{company}/DCPK/{token}/{token2}", function ( $company, $token, $token2 ) {
//    switch ( $company ) {
//        case "harir":
//            return redirect()->away( "http://109.125.144.51:8085/DCPK/" . $token . "/" . $token2 );
//        case "arman":
//            return redirect()->away( "http://109.125.144.51:8086/DCPK/" . $token . "/" . $token2 );
//    }
//} );


