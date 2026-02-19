<?php

use App\Http\Controllers\HR\Definition;
use App\Http\Controllers\HR\Employment;
use App\Http\Controllers\HR\EvaluationForm;
use App\Http\Controllers\HR\Personal;
use App\Http\Controllers\HR\PersonalController;
use App\Http\Controllers\HR\Post;
use App\Http\Controllers\HR\PostController;
use App\Http\Controllers\HR\Setting;
use App\Http\Controllers\HR\ShiftController;
use App\Http\Controllers\HR\ShiftDelivery;
use App\Http\Controllers\HR\Worker\PersonalFileController;
use App\Http\Controllers\HR\WorkerController;
use App\Http\Controllers\HR\Chat\ChatifyController;
use App\Http\Controllers\HR;



Route::middleware(['url_check:hr.post.index'])->prefix('post')->name("post.")->group(function () {

    Route::match(['get', 'post'], "index", [PostController::class, "index"])->name("index");
    Route::get("edit/{post}", [PostController::class, "edit"])->name("edit");
    Route::post("update_info/{post}", [PostController::class, "update_info"])->name("update.info");
    Route::post("info_setting/{post}", [PostController::class, "info_setting"])->name("update.info_setting");
    Route::get("add_user/{post}", [PostController::class, "add_user"])->name("add_user");
    Route::post("submit_add_user/{post}", [
        PostController::class,
        "submit_add_user"
    ])->name("submit_add_user");
    Route::get("delete_user/{post}/{user}", [
        PostController::class,
        "delete_user"
    ])->name("delete_user");


    Route::get("machine_module_type_list/{post}/{machine_module_type}", [
        PostController::class,
        "machine_module_type_list"
    ])->name("machine_module_type_list");
    Route::get("edit_module/{post}/{goods_kind}/{status_type_id}", [
        PostController::class,
        "edit_module"
    ])->name("edit_module");
    Route::post("submit_edit_module/{post}/{goods_kind}/{status_type_id}", [
        PostController::class,
        "submit_edit_module"
    ])->name("submit_edit_module");

//Route::get("add_users/{post}/{user}", [PostController::class, "add_users"])->name("add_my_user");

    Route::get("edit/{post}", [PostController::class, "edit"])->name("edit");
    Route::get("edit_setting/{post}", [PostController::class, "edit_setting"])->name("edit_setting");

#line station machine_type machine access
    Route::get("add_access/{post}/{line_id}/{station_id}/{machine_type_id}/{machine_id}",
        [PostController::class, "add_access"])->name("add_access"
    );
    Route::get("remove_access/{post}/{line_id}/{station_id}/{machine_type_id}/{machine_id}",
        [PostController::class, "remove_access"])->name("remove_access"
    );
    Route::get("manage_access/{post}/{line_id}/{station_id}/{machine_type_id}/{machine_id}",
        [PostController::class, "manage_access"])->name("manage_access"
    );
    Route::post("machine_permission/{post}/{machine_type}",
        [PostController::class, "machine_permission"])->name("machine_permission"
    );


    Route::get("manage_machine_type_status/{post}/{machine_type}",
        [PostController::class, "manage_machine_type_status"])->name("manage_machine_type_status"
    );
    Route::post("submit_manage_machine_type_status/{post}/{machine_type}",
        [PostController::class, "submit_manage_machine_type_status"])->name("submit_manage_machine_type_status"
    );

    Route::get("create", [PostController::class, "create"])->name("create");
    Route::post("insert", [PostController::class, "insert"])->name("insert");


    Route::prefix('smart_object')->name("smart_object.")->group(function () {
        Route::get("index/{post}", [Post\SmartController::class, "index"])->name("index");
        Route::post("submit/{post}", [Post\SmartController::class, "submit"])->name("submit");
    });
    Route::name("post_in_ic.")->prefix("post_in_ic")->group(function () {
        Route::get("index/{post}/{search??}", [Post\PostInIcController::class, "index"])->name("index");
        Route::post("search/{post}", [Post\PostInIcController::class, "search"])->name("search");
        Route::get("update/{post}/{post_id}", [Post\PostInIcController::class, "update"])->name("update");
    });
    Route::name("post_selection.")->prefix("post_selection")->group(function () {


        Route::get("create/{post}", [Post\PostSelectionController::class, "create"])->name("create");
        Route::post("store/{post}", [Post\PostSelectionController::class, "store"])->name("store");
        Route::get('/destroy/{post}/{post_selection_setting}', [Post\PostSelectionController::class, "destroy"])->name('destroy');


        Route::get("add_selector/{selection}/{post}", [Post\PostSelectionController::class, "add_selector"])->name("add_selector");
        Route::post("store_add_selector/{selection}/{post}", [Post\PostSelectionController::class, "store_add_selector"])->name("store_add_selector");
        Route::get("destroy_add_selector/{selection_selector}/{selection}/{post}", [Post\PostSelectionController::class, "destroy_add_selector"])->name("destroy_add_selector");
        Route::get("percent_of_committee/{selection}/{post}", [Post\PostSelectionController::class, "percent_of_committee"])->name("percent_of_committee");
        Route::post("submit_percent_of_committee/{selection}/{post}", [Post\PostSelectionController::class, "submit_percent_of_committee"])->name("submit_percent_of_committee");

    });
    Route::name("post_selection_education.")->prefix("post_selection_education")->group(function () {

        Route::get("create/{post}/{selection}/{post_selection_setting}", [Post\PostSelectionEducationController::class, "create"])->name("create");
        Route::post("store/{post}/{selection}/{post_selection_setting}", [Post\PostSelectionEducationController::class, "store"])->name("store");
        Route::get('/destroy/{post}/{selection}/{post_selection_setting}/{post_selection_education}', [Post\PostSelectionEducationController::class, "destroy"])->name('destroy');
    });


    //روت مربوط به فرم ارزیابی
    Route::name("post_evaluation.")->prefix("post_evaluation")->group(function () {
        Route::get("index/{post}", [Post\PostEvaluationController::class, "index"])->name("index");
        Route::get('edit/{post}/{evaluation_type}', [Post\PostEvaluationController::class, "edit"])->name('edit');
        Route::post('update/{post_evaluation}', [Post\PostEvaluationController::class, "update"])->name('update');
        Route::get('add_indicator/{post_evaluation}', [Post\PostEvaluationController::class, "add_indicator"])->name('add_indicator');
        Route::post('store_indicator/{post_evaluation}', [Post\PostEvaluationController::class, "store_indicator"])->name('store_indicator');
        Route::get('destroy_indicator/{post_evaluation}/{post_evaluation_indicator}', [Post\PostEvaluationController::class, "destroy_indicator"])->name('destroy_indicator');
    });
    //جذب و استخدام
    Route::name("employment.")->prefix("employment")->group(function () {
        Route::get("index/{post}", [Post\EmploymentController::class, "index"])->name("index");
        Route::post('info_setting/{post}', [Post\EmploymentController::class, "info_setting"])->name('info_setting');
    });
    //گفتگوی انلاین
    Route::name("chat.")->prefix("chat")->group(function () {
        Route::get("index/{post}", [Post\ChatSettingController::class, "index"])->name("index");
        Route::post('submit/{post}', [Post\ChatSettingController::class, "submit"])->name('submit');
    });
    Route::name("entry_status_permission.")->prefix("entry_status_permission")->group(function () {
        Route::get("index/{post}", [Post\EntryStatusPermissionController::class, "index"])->name("index");
        Route::post('submit/{post}', [Post\EntryStatusPermissionController::class, "submit"])->name('submit');
    });
});

Route::middleware(['url_check:hr.worker.index'])->prefix('worker')->name("worker.")->group(function () {

    Route::match(['get', 'post'], "index", [WorkerController::class, "index"])->name("index");
    Route::get("edit/{worker}", [WorkerController::class, "edit"])->name("edit");
    Route::post("update_info/{worker}", [
        WorkerController::class,
        "update_info"
    ])->name("update.info");

    Route::get("create", [WorkerController::class, "create"])->name("create");
    Route::post("store", [WorkerController::class, "store"])->name("store");
    Route::get("print_worker_card/{worker}", [
        WorkerController::class,
        "print_worker_card"
    ])->name("print_worker_card");

    Route::name("personal_file.")->prefix("personal_file")->group(function () {
        Route::get("index/{worker}", [PersonalFileController::class, "index"])->name("index");
    });

    Route::name("overtime_together.")->prefix("overtime_together")->group(function () {
        Route::get("index", [HR\Worker\OvertimeTogetherController::class, "index"])->name("index");
        Route::post("submit", [HR\Worker\OvertimeTogetherController::class, "submit"])->name("submit");
    });


});


Route::middleware(['url_check:hr.shift.index'])->name("shift.")->prefix('shift')->group(function () {

    Route::get("index", [ShiftController::class, "index"])->name("index");

    Route::get("create", [ShiftController::class, "create"])->name("create");
    Route::post("store", [
        ShiftController::class,
        "store"
    ])->name("store");

    Route::get("edit/{shift}", [ShiftController::class, "edit"])->name("edit");

    Route::post("update/{shift}", [ShiftController::class, "update"])->name("update");

    Route::get("edit_shift_group/{shift}", [ShiftController::class, "edit_shift_group"])->name("edit_shift_group");
    Route::post("update_shift_group/{shift}", [ShiftController::class, "update_shift_group"])->name("update_shift_group");

    Route::get("upload_shift_work/{shift}", [
        ShiftController::class,
        "upload_shift_work"
    ])->name("upload_shift_work");

    Route::post("submit_shift_work/{shift}", [
        ShiftController::class,
        "submit_shift_work"
    ])->name("submit_shift_work");

    Route::get("show_shift_work/{shift}/{check_avg}", [ShiftController::class, "show_shift_work"])->name("show_shift_work");

    Route::get("confirm_shift_work/{shift}", [
        ShiftController::class,
        "confirm_shift_work"
    ])->name("confirm_shift_work");


    Route::get("calc_entry_log_for_days", [
        ShiftController::class,
        "calc_entry_log_for_days"
    ])->name("calc_entry_log_for_days");

    Route::post("submit_calc_entry_log_for_days", [
        ShiftController::class,
        "submit_calc_entry_log_for_days"
    ])->name("submit_calc_entry_log_for_days");


});

Route::name("personal.")->prefix("/personal")->group(function () {

    Route::get("index/{worker}/{key}/{back_url?}", [
        PersonalController::class,
        "index"
    ])->prefix("/index")->name("index");

    Route::get("current_user", [
        PersonalController::class,
        "current_user"
    ])->prefix("/current_user")->name("current_user");

    Route::name("confirm_entry.")->prefix("/confirm_entry")->group(function () {

        Route::post("submit/{worker}", [
            Personal\ConfirmEntryController::class,
            "submit"
        ])->prefix("/index")->name("submit");
    });
    Route::name("start_remote_work.")->prefix("/start_remote_work")->group(function () {

        Route::post("submit/{worker}", [
            Personal\StartRemoteWorkController::class,
            "submit"
        ])->prefix("/index")->name("submit");
    });
    Route::name("end_remote_work.")->prefix("/end_remote_work")->group(function () {

        Route::post("submit/{worker}", [
            Personal\EndRemoteWorkController::class,
            "submit"
        ])->prefix("/index")->name("submit");
    });
    Route::name("confirm_exit.")->prefix("/confirm_exit")->group(function () {

        Route::post("submit/{worker}", [
            Personal\ConfirmExitController::class,
            "submit"
        ])->prefix("submit")->name("submit");
    });

    Route::name("confirm_illegal_exit.")->prefix("/confirm_illegal_exit")->group(function () {

        Route::post("submit/{worker}", [
            Personal\ConfirmIllegalExistController::class,
            "submit"
        ])->prefix("submit")->name("submit");
    });

    Route::post("confirm_parent_post", [
        PersonalController::class,
        "confirm_parent_post"
    ])->name("confirm_parent_post");

    Route::post("set_user_comment", [
        PersonalController::class,
        "set_user_comment"
    ])->name("set_user_comment");


    Route::get("select_smart_object/{smart_object_type}/{back_route}/{param1}", [
        PersonalController::class,
        "select_smart_object"
    ])->name("select_smart_object");

    Route::post("submit_select_smart_object/{smart_object_type}", [
        PersonalController::class,
        "submit_select_smart_object"
    ])->name("submit_select_smart_object");

    Route::name("user_device.")->prefix("/user_device")->group(function () {
        Route::get("index/{worker}", [
            Personal\UserDeviceController::class,
            "index"
        ])->prefix("index")->name("index");

        Route::get("destroy/{user_device}", [
            Personal\UserDeviceController::class,
            "destroy"
        ])->prefix("destroy")->name("destroy");
    });
    Route::name("leave.")->prefix("/leave")->group(function () {

        Route::get("index/{worker_id?}", [
            Personal\LeaveController::class,
            "index"
        ])->prefix("index")->name("index");

        Route::post("submit", [
            Personal\LeaveController::class,
            "submit"
        ])->prefix("submit")->name("submit");

        Route::get("replace_work", [
            Personal\LeaveController::class,
            "replace_work"
        ])->prefix("replace_work")->name("replace_work");

        Route::post("submit_replace_work", [
            Personal\LeaveController::class,
            "submit_replace_work"
        ])->prefix("submit_replace_work")->name("submit_replace_work");

        Route::get("log/{leave_overtime}", [
            Personal\LeaveController::class,
            "log"
        ])->prefix("log")->name("log");

        Route::get("cancel/{leave_overtime}", [
            Personal\LeaveController::class,
            "cancel"
        ])->prefix("cancel")->name("cancel");

        Route::get("confirm_replace_user/{leave_overtime}", [
            Personal\LeaveController::class,
            "confirm_replace_user"
        ])->prefix("confirm_replace_user")->name("confirm_replace_user");

        Route::get("reject_replace_user/{leave_overtime}", [
            Personal\LeaveController::class,
            "reject_replace_user"
        ])->prefix("reject_replace_user")->name("reject_replace_user");


    });

    Route::name("absence.")->prefix("/absence")->group(function () {

        Route::get("log/{leave_overtime}", [
            Personal\LeaveController::class,
            "log"
        ])->prefix("log")->name("log");


        Route::get("confirm_replace_user/{leave_overtime}", [
            Personal\AbsenceController::class,
            "confirm_replace_user"
        ])->prefix("confirm_replace_user")->name("confirm_replace_user");

        Route::get("reject_replace_user/{leave_overtime}", [
            Personal\AbsenceController::class,
            "reject_replace_user"
        ])->prefix("reject_replace_user")->name("reject_replace_user");


    });

    Route::name("overtime.")->prefix("/overtime")->group(function () {

        Route::get("index", [
            Personal\OvertimeController::class,
            "index"
        ])->prefix("index")->name("index");

        Route::post("submit", [
            Personal\OvertimeController::class,
            "submit"
        ])->prefix("submit")->name("submit");

        Route::get("replace_work", [
            Personal\OvertimeController::class,
            "replace_work"
        ])->prefix("replace_work")->name("replace_work");

        Route::post("submit_replace_work", [
            Personal\OvertimeController::class,
            "submit_replace_work"
        ])->prefix("submit_replace_work")->name("submit_replace_work");

        Route::get("log/{leave_overtime}", [
            Personal\OvertimeController::class,
            "log"
        ])->prefix("log")->name("log");

        Route::get("cancel/{leave_overtime}", [
            Personal\OvertimeController::class,
            "cancel"
        ])->prefix("cancel")->name("cancel");

    });

    Route::name("replacement.")->prefix("/replacement")->group(function () {

        Route::get("index", [
            Personal\ReplacementController::class,
            "index"
        ])->prefix("index")->name("index");

        Route::post("submit", [
            Personal\ReplacementController::class,
            "submit"
        ])->prefix("submit")->name("submit");

        Route::get("replace_work", [
            Personal\ReplacementController::class,
            "replace_work"
        ])->prefix("replace_work")->name("replace_work");

        Route::post("submit_replace_work", [
            Personal\ReplacementController::class,
            "submit_replace_work"
        ])->prefix("submit_replace_work")->name("submit_replace_work");

        Route::get("log/{leave_overtime}", [
            Personal\ReplacementController::class,
            "log"
        ])->prefix("log")->name("log");

        Route::get("cancel/{leave_overtime}", [
            Personal\ReplacementController::class,
            "cancel"
        ])->prefix("cancel")->name("cancel");


        Route::get("confirm_replace_user/{leave_overtime}", [
            Personal\ReplacementController::class,
            "confirm_replace_user"
        ])->prefix("confirm_replace_user")->name("confirm_replace_user");

        Route::get("reject_replace_user/{leave_overtime}", [
            Personal\ReplacementController::class,
            "reject_replace_user"
        ])->prefix("reject_replace_user")->name("reject_replace_user");


    });

    Route::name("mission.")->prefix("/mission")->group(function () {

        Route::get("index", [
            Personal\MissionController::class,
            "index"
        ])->name("index");

        Route::post("submit", [
            Personal\MissionController::class,
            "submit"
        ])->name("submit");

        Route::get("replace_work", [
            Personal\MissionController::class,
            "replace_work"
        ])->name("replace_work");

        Route::post("submit_replace_work", [
            Personal\MissionController::class,
            "submit_replace_work"
        ])->name("submit_replace_work");

        Route::get("log/{leave_overtime}", [
            Personal\MissionController::class,
            "log"
        ])->name("log");

        Route::get("cancel/{leave_overtime}", [
            Personal\MissionController::class,
            "cancel"
        ])->name("cancel");
        Route::get("confirm_replace_user/{leave_overtime}", [
            Personal\MissionController::class,
            "confirm_replace_user"
        ])->prefix("confirm_replace_user")->name("confirm_replace_user");

        Route::get("reject_replace_user/{leave_overtime}", [
            Personal\MissionController::class,
            "reject_replace_user"
        ])->prefix("reject_replace_user")->name("reject_replace_user");


    });


    Route::name("shift_work_day.")->prefix("/shift_work_day")->group(function () {

        Route::get("index/{worker}/{year?}/{month?}", [
            Personal\ShiftWorkDayController::class,
            "index"
        ])->name("index");
        Route::get("download_operation_list/{worker}/{year?}/{month?}", [
            Personal\ShiftWorkDayController::class,
            "download_operation_list"
        ])->name("download_operation_list");
        Route::get("show_entry_log_for_day/{worker}/{user_operation}", [
            Personal\ShiftWorkDayController::class,
            "show_entry_log_for_day"
        ])->name("show_entry_log_for_day");

        Route::get("show_entry_log_calc/{worker}/{user_operation}", [
            Personal\ShiftWorkDayController::class,
            "show_entry_log_calc"
        ])->name("show_entry_log_calc");


    });


    Route::name("chart.")->prefix("/chart")->group(function () {

        Route::get("show_chart", [
            Personal\ChartController::class,
            "show_chart"
        ])->prefix("show_chart")->name("show_chart");
    });

    Route::name("evaluation_form.")->prefix("/evaluation_form")->group(function () {

        Route::get("index/{user_id?}", [
            Personal\EvaluationFormController::class,
            "index"
        ])->prefix("index")->name("index");

        Route::get("view/{evaluation_form}", [
            Personal\EvaluationFormController::class,
            "view"
        ])->prefix("view")->name("view");
    });

});

Route::middleware(['url_check:hr.setting.dashboard.index'])->name("setting.")->prefix("/setting")->group(function () {

    Route::name("dashboard.")->prefix("/dashobard")->group(function () {
        Route::get("index", [Setting\DashboardController::class, "index"])->name("index");
        Route::get("edit_module/{shift_delivery_module}", [
            Setting\DashboardController::class,
            "edit_module"
        ])->name("edit_module");
        Route::post("update_module/{shift_delivery_module}", [
            Setting\DashboardController::class,
            "update_module"
        ])->name("update_bank");
        Route::post("update_bank", [
            Setting\DashboardController::class,
            "update_bank"
        ])->name("update_bank");
        Route::post("update_employment_notification", [
            Setting\DashboardController::class,
            "update_employment_notification"
        ])->name("update_employment_notification");
    });

});

Route::name("shift_delivery.")->prefix("/shift_delivery")->group(function () {

    Route::name("module1.")->prefix("/module1")->group(function () {
        Route::get("index/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "index"
        ])->name("index");
        Route::post("submit/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "submit"
        ])->name("submit");
        Route::get("waiting_delivery/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "waiting_delivery"
        ])->name("waiting_delivery");
        Route::post("submit_waiting_delivery/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "submit_waiting_delivery"
        ])->name("submit_waiting_delivery");
        Route::get("show_efficiency/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "show_efficiency"
        ])->name("show_efficiency");
    });

    Route::name("module3.")->prefix("/module3")->group(function () {
        Route::get("index/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "index"
        ])->name("index");
    });
    Route::name("module2.")->prefix("/module2")->group(function () {
        Route::get("index/{shift_delivery_module}/{post}", [
            ShiftDelivery\ShiftDeliveryModule1Controller::class,
            "index"
        ])->name("index");
    });

    Route::name("illegal_delivery.")->prefix("/illegal_delivery")->group(function () {
        Route::get("index/{post}", [
            ShiftDelivery\IlegalDeliveryModuleController::class,
            "index"
        ])->name("index");
        Route::post("submit/{post}", [
            ShiftDelivery\IlegalDeliveryModuleController::class,
            "submit"
        ])->name("submit");
    });
});


Route::name("definition.")->prefix("/definition")->group(function () {

    Route::middleware(['url_check:hr.definition.committee.index'])->name("committee.")->prefix("committee")->group(function () {

        Route::get("index", [Definition\CommitteeController::class, "index"])->name("index");
        Route::get("create", [Definition\CommitteeController::class, "create"])->name("create");
        Route::post("store", [Definition\CommitteeController::class, "store"])->name("store");
        Route::get("edit/{committee}", [Definition\CommitteeController::class, "edit"])->name("edit");
        Route::post("update/{committee}", [Definition\CommitteeController::class, "update"])->name("update");
        Route::get("destroy/{committee}", [Definition\CommitteeController::class, "destroy"])->name("destroy");
    });

//
    Route::middleware(['url_check:hr.definition.education.education.index'])->name("education.")->prefix("education")->group(function () {

        Route::name("education.")->prefix("education")->group(function () {
            Route::get("create", [Definition\Education\EducationController::class, "create"])->name("create");
            Route::post("store", [Definition\Education\EducationController::class, "store"])->name("store");
            Route::get("index", [Definition\Education\EducationController::class, "index"])->name("index");
            Route::get('/destroy/{id}', [Definition\Education\EducationController::class, "destroy"])->name('destroy');
            Route::get('/edit/{education}', [Definition\Education\EducationController::class, "edit"])->name('edit');
            Route::post('/update/{education}', [Definition\Education\EducationController::class, "update"])->name('update');
            Route::get('/download/{education}/{file}', [Definition\Education\EducationController::class, "download"])->name('download');
        });
    });

    Route::middleware(['url_check:hr.definition.evaluation.evaluation.index'])->name("evaluation.")->prefix("evaluation")->group(function () {

        Route::name("evaluation.")->prefix("evaluation")->group(function () {
            Route::get("index", [Definition\Evaluation\EvaluationIndicatorController::class, "index"])->name("index");
            Route::get("create", [Definition\Evaluation\EvaluationIndicatorController::class, "create"])->name("create");
            Route::post("store", [Definition\Evaluation\EvaluationIndicatorController::class, "store"])->name("store");
            Route::get('edit/{evaluation_indicator}', [Definition\Evaluation\EvaluationIndicatorController::class, "edit"])->name('edit');
            Route::post('/update/{evaluation_indicator}', [Definition\Evaluation\EvaluationIndicatorController::class, "update"])->name('update');
        });
    });

    Route::middleware(['url_check:hr.definition.selection.selection.index'])->name("selection.")->prefix("selection")->group(function () {

        Route::name("selection.")->prefix("selection")->group(function () {
            Route::get("create", [Definition\Selection\SelectionController::class, "create"])->name("create");
            Route::post("store", [Definition\Selection\SelectionController::class, "store"])->name("store");
            Route::get("index", [Definition\Selection\SelectionController::class, "index"])->name("index");
            Route::get('/destroy/{id}', [Definition\Selection\SelectionController::class, "destroy"])->name('destroy');
            Route::get('/edit/{selection}', [Definition\Selection\SelectionController::class, "edit"])->name('edit');
            Route::post('/update/{selection}', [Definition\Selection\SelectionController::class, "update"])->name('update');
        });
        Route::name("selection_indicator.")->prefix("selection_indicator")->group(function () {
            Route::get("create/{selection}", [Definition\Selection\SelectionIndicatorController::class, "create"])->name("create");
            Route::post("store/{selection}", [Definition\Selection\SelectionIndicatorController::class, "store"])->name("store");
            Route::get('/destroy/{selection}/{selection_indicator}', [Definition\Selection\SelectionIndicatorController::class, "destroy"])->name('destroy');
            Route::get('/edit/{selection_indicator}', [Definition\Selection\SelectionIndicatorController::class, "edit"])->name('edit');
            Route::post('update/{selection_indicator}', [Definition\Selection\SelectionIndicatorController::class, "update"])->name('update');
        });
    });
});

Route::name("employment.")->prefix("employment")->group(function () {
    Route::name("admin.")->prefix("admin")->group(function () {


        Route::name("direct_register.")->prefix("direct_register")->group(function () {
            Route::get("index", [Employment\Admin\DirectRegisterController::class, "index"])->name("index");
        });
        Route::name("confirm.")->prefix("confirm")->group(function () {
            Route::name("personal_info.")->prefix("personal_info")->group(function () {
                Route::get("confirm/{employment}", [Employment\Admin\Confirm\PersonalInfoController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\PersonalInfoController::class, "reject"])->name("reject");

            });

            Route::name("address.")->prefix("address")->group(function () {

                Route::get("confirm/{employment}", [Employment\Admin\Confirm\AddressController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\AddressController::class, "reject"])->name("reject");

            });
            Route::name("company.")->prefix("company")->group(function () {

                Route::get("confirm/{employment}", [Employment\Admin\Confirm\CompanyInfoController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\CompanyInfoController::class, "reject"])->name("reject");

            });

            Route::name("dependent.")->prefix("dependent")->group(function () {

                Route::get("confirm/{employment}", [Employment\Admin\Confirm\DependentController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\DependentController::class, "reject"])->name("reject");

            });
            Route::name("academic_degree.")->prefix("academic_degree")->group(function () {

                Route::get("confirm/{employment}", [Employment\Admin\Confirm\AcademicDegreeController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\AcademicDegreeController::class, "reject"])->name("reject");

            });
            Route::name("job_information.")->prefix("job_information")->group(function () {
                Route::get("confirm/{employment}", [Employment\Admin\Confirm\JobInformationController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\JobInformationController::class, "reject"])->name("reject");

            });
            Route::name("educational_course.")->prefix("educational_course")->group(function () {
                Route::get("confirm/{employment}", [Employment\Admin\Confirm\EducationalCourseController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\EducationalCourseController::class, "reject"])->name("reject");

            });
            Route::name("upload_document.")->prefix("upload_document")->group(function () {
                Route::get('/download/{employment}/{employment_document_type}', [Employment\Admin\Confirm\UploadDocumentController::class, "download"])->name('download');
                Route::get("confirm/{employment}", [Employment\Admin\Confirm\UploadDocumentController::class, "confirm"])->name("confirm");
                Route::post("reject/{employment}", [Employment\Admin\Confirm\UploadDocumentController::class, "reject"])->name("reject");

            });
        });
        Route::name("contractor.")->prefix("contractor")->group(function () {
            Route::name("confirm_draft_information.")->prefix("confirm_draft_information")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Contractor\ConfirmDraftInformationController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Contractor\ConfirmDraftInformationController::class, "submit"])->name("submit");

            });
            Route::name("drafting_contract.")->prefix("drafting_contract")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Contractor\DraftingContractController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Contractor\DraftingContractController::class, "submit"])->name("submit");

            });
            Route::name("drafting_contract_final.")->prefix("drafting_contract_final")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Contractor\DraftingContractFinalController::class, "submit"])->name("submit");
                Route::get("index/{employment}", [Employment\Admin\Contractor\DraftingContractFinalController::class, "index"])->name("index");

            });
            Route::name("drafting_contract_init.")->prefix("drafting_contract_init")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Contractor\DraftingContractInitController::class, "submit"])->name("submit");
                Route::get("index/{employment}", [Employment\Admin\Contractor\DraftingContractInitController::class, "index"])->name("index");

            });
            Route::name("registration_cost_center.")->prefix("registration_cost_center")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Contractor\RegistrationCostCenterController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Contractor\RegistrationCostCenterController::class, "submit"])->name("submit");

            });
            Route::name("delivery_of_document.")->prefix("delivery_of_document")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Contractor\DeliveryOfDocumentController::class, "submit"])->name("submit");

            });
            Route::name("cost_center.")->prefix("cost_center")->group(function () {
                Route::get("create/{employment}", [Employment\Admin\Contractor\CostCenterController::class, "create"])->name("create");
                Route::post("store/{employment}", [Employment\Admin\Contractor\CostCenterController::class, "store"])->name("store");

            });
        });
        Route::name("supplier.")->prefix("supplier")->group(function () {
            Route::name("confirm_draft_information.")->prefix("confirm_draft_information")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Supplier\ConfirmDraftInformationController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Supplier\ConfirmDraftInformationController::class, "submit"])->name("submit");

            });
            Route::name("drafting_contract.")->prefix("drafting_contract")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Supplier\DraftingContractController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Supplier\DraftingContractController::class, "submit"])->name("submit");

            });
            Route::name("drafting_contract_final.")->prefix("drafting_contract_final")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Supplier\DraftingContractFinalController::class, "submit"])->name("submit");
                Route::get("index/{employment}", [Employment\Admin\Supplier\DraftingContractFinalController::class, "index"])->name("index");

            });
            Route::name("drafting_contract_init.")->prefix("drafting_contract_init")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Supplier\DraftingContractInitController::class, "submit"])->name("submit");
                Route::get("index/{employment}", [Employment\Admin\Supplier\DraftingContractInitController::class, "index"])->name("index");

            });
            Route::name("registration_cost_center.")->prefix("registration_cost_center")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Supplier\RegistrationCostCenterController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Supplier\RegistrationCostCenterController::class, "submit"])->name("submit");

            });
            Route::name("delivery_of_document.")->prefix("delivery_of_document")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Supplier\DeliveryOfDocumentController::class, "submit"])->name("submit");

            });
            Route::name("cost_center.")->prefix("cost_center")->group(function () {
                Route::get("create/{employment}", [Employment\Admin\Supplier\CostCenterController::class, "create"])->name("create");
                Route::post("store/{employment}", [Employment\Admin\Supplier\CostCenterController::class, "store"])->name("store");

            });
        });
        Route::name("customer.")->prefix("customer")->group(function () {

            Route::name("confirm_draft_information.")->prefix("confirm_draft_information")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Customer\ConfirmDraftInformationController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Customer\ConfirmDraftInformationController::class, "submit"])->name("submit");

            });
            Route::name("drafting_contract.")->prefix("drafting_contract")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Customer\DraftingContractController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Customer\DraftingContractController::class, "submit"])->name("submit");

            });
            Route::name("drafting_contract_final.")->prefix("drafting_contract_final")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Customer\DraftingContractFinalController::class, "submit"])->name("submit");
                Route::get("index/{employment}", [Employment\Admin\Customer\DraftingContractFinalController::class, "index"])->name("index");
                Route::get("download_contract/{employment}", [Employment\Admin\Customer\DraftingContractFinalController::class, "download_contract"])->name("download_contract");

            });
            Route::name("drafting_contract_init.")->prefix("drafting_contract_init")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Customer\DraftingContractInitController::class, "submit"])->name("submit");
                Route::get("index/{employment}", [Employment\Admin\Customer\DraftingContractInitController::class, "index"])->name("index");

            });
            Route::name("registration_cost_center.")->prefix("registration_cost_center")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Customer\RegistrationCostCenterController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Customer\RegistrationCostCenterController::class, "submit"])->name("submit");

            });
            Route::name("delivery_of_document.")->prefix("delivery_of_document")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Customer\DeliveryOfDocumentController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Customer\DeliveryOfDocumentController::class, "submit"])->name("submit");

            });
            Route::name("tariff.")->prefix("tariff")->group(function () {
                Route::get("create/{employment}", [Employment\Admin\Customer\TariffController::class, "create"])->name("create");
                Route::post("store/{employment}", [Employment\Admin\Customer\TariffController::class, "store"])->name("store");

            });
            Route::name("cost_center.")->prefix("cost_center")->group(function () {
                Route::get("create/{employment}", [Employment\Admin\Customer\CostCenterController::class, "create"])->name("create");
                Route::post("store/{employment}", [Employment\Admin\Customer\CostCenterController::class, "store"])->name("store");

            });
        });
        Route::name("confirm_personal_info.")->prefix("confirm_personal_info")->group(function () {
            Route::get("submit/{employment}", [Employment\Admin\ConfirmPersonalInfoController::class, "submit"])->name("submit");

        });
        Route::name("dashboard.")->prefix("dashboard")->group(function () {
            Route::match(['get', 'post'], "index", [Employment\Admin\DashboardController::class, "index"])->name("index");
            Route::get("letter/{employment}", [Employment\Admin\DashboardController::class, "letter"])->name("letter");
            Route::get("view/{employment}", [Employment\Admin\DashboardController::class, "view"])->name("view");
            Route::get("view_selection_result/{employment}/{employment_selection}", [Employment\Admin\DashboardController::class, "view_selection_result"])->name("view_selection_result");
        });
        Route::name("personal.")->prefix("personal")->group(function () {
            Route::name("coordination_selection.")->prefix("coordination_selection")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Personal\CoordinationSelectionController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Personal\CoordinationSelectionController::class, "submit"])->name("submit");
            });
            Route::name("registration_of_selection_result.")->prefix("registration_of_selection_result")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Personal\RegistrationOfSelectionResultController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Personal\RegistrationOfSelectionResultController::class, "submit"])->name("submit");
            });
            Route::name("delivery_of_document.")->prefix("delivery_of_document")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Personal\DeliveryOfDocumentController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Personal\DeliveryOfDocumentController::class, "submit"])->name("submit");
            });
            Route::name("financial_information.")->prefix("financial_information")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Personal\FinancialInformationController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Personal\FinancialInformationController::class, "submit"])->name("submit");
            });
            Route::name("cost_center.")->prefix("cost_center")->group(function () {
                Route::get("create/{employment}", [Employment\Admin\Personal\CostCenterController::class, "create"])->name("create");
                Route::post("store/{employment}", [Employment\Admin\Personal\CostCenterController::class, "store"])->name("store");

            });
            Route::name("internet_account.")->prefix("internet_account")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Personal\InternetAccountController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Personal\InternetAccountController::class, "submit"])->name("submit");
            });

            Route::name("request_reject.")->prefix("request_reject")->group(function () {
                Route::post("submit/{employment}", [Employment\Admin\Personal\RequestRejectController::class, "submit"])->name("submit");
            });


            Route::name("update_document.")->prefix("update_document")->group(function () {
                Route::get("index/{employment}", [Employment\Admin\Personal\UpdateDocumentController::class, "index"])->name("index");
                Route::post("submit/{employment}", [Employment\Admin\Personal\UpdateDocumentController::class, "submit"])->name("submit");
            });
        });
    });
});

//روت مربوط به فرم ارزیابی
Route::name("evaluation_form.")->prefix("evaluation_form")->group(function () {
    Route::name("dashboard.")->prefix("dashboard")->group(function () {
        Route::get("index", [EvaluationForm\DashboardController::class, "index"])->name("index");
        Route::get("confirm_indicator/{evaluation_form_export}", [EvaluationForm\DashboardController::class, "confirm_indicator"])->name("confirm_indicator");
        Route::post('store_indicator/{evaluation_form_export}', [EvaluationForm\DashboardController::class, "store_indicator"])->name('store_indicator');
    });
});


Route::middleware(['url_check:hr.chat.index'])->name("chat.")->prefix("chat")->group(function () {

    Route::get("index", [ChatifyController::class, "index"])->name("index");

});
Route::name("company.")->prefix("company")->group(function () {
    Route::name("dashboard.")->prefix("dashboard")->group(function () {
        Route::get("index", [HR\Company\dashboardController::class, "index"])->name("index");

    });
});


