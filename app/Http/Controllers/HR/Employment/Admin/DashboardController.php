<?php

namespace App\Http\Controllers\HR\Employment\Admin;

use App\Http\Controllers\Controller;
use App\Http\Controllers\HR\Employment\Admin\Personal\CoordinationSelectionController;
use App\Http\Controllers\HR\Employment\Admin\Personal\DeliveryOfDocumentController;
use App\Http\Controllers\HR\Employment\Admin\Personal\FinancialInformationController;
use App\Http\Controllers\HR\Employment\Admin\Personal\InternetAccountController;
use App\Http\Controllers\HR\Employment\Admin\Personal\RegistrationOfSelectionResultController;
use App\Http\Controllers\HR\Employment\Admin\Personal\RequestRejectController;
use App\Http\Controllers\HR\Employment\Admin;
use App\Http\Controllers\HR\Employment\Register\Personal\WorkMedicineController;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Employment\EmploymentLog;
use App\Models\HR\Employment\EmploymentSelection;
use App\Models\HR\Personal\Personal;
use App\Models\HR\User\UserAcademicDegree;
use App\Models\Post\PostStatus;
use App\Models\Post\PostUser;
use App\Models\Utility\Option;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DashboardController extends Controller
{
    // hr/employment/admin/dashboard
    public static $perfix_status_code = "4640";
    protected $view_path = "hr.employment.admin.dashboard.";
    protected $route_path = "hr.employment.admin.dashboard.";


    public function index(Request $request)
    {


         $post_user = Auth::user()->posts->first();
         $allow_direct_register=$post_user->checkButtonPermission(DirectRegisterController::$info["route"] . "index");



        $allowed_status_ids = PostStatus::getAllowedStatus(5);

        $black_lists = [-1];
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $employment_ids = Employment::where("status_id", 4640103)->pluck('id');
        $employment_selections = EmploymentSelection::
        whereIn('employment_id', $employment_ids)->
        where("status_id", 4640103)->
        get();
        $post_user = Auth::user()->posts->first();
        $active_tab = $post_user->checkButtonPermission("hr.employment.admin.personal.registration_of_selection_result.show_all");
        if (!$active_tab) {
            foreach ($employment_selections as $employment_selection) {
                if ($employment_selection->priority_number == $employment_selection->employment->current_priority_number) {
                    $black_lists[$employment_selection->employment_id] = RegistrationOfSelectionResultController::get_special_condition($employment_selection, $post_ids);
                }
            }

            foreach ($black_lists as $key => $item) {
                if ($item) {
                    unset($black_lists[$key]);
                }
            }
        }
        if ($request->isMethod('post')) {
            $search = $request->search;
            $status_id = $request->status_id;
            $cooperation_type_id = $request->cooperation_type_id;

        } else {
            $search = session("search_employment");
            $status_id = session("status_id_employment");
            $cooperation_type_id = session("cooperation_type_employment");


        }
        session([
            "search_employment" => $search,
            "status_id_employment" => $status_id,
            "cooperation_type_employment" => $cooperation_type_id,
        ]);
        if ($status_id != 0) {
            $allowed_status_ids = [];
            $allowed_status_ids[] = $status_id;
        }
        $list = Employment::
        join('users', 'users.id', 'user_id')->
        leftJoin('companies', 'companies.id', 'company_id')->

        whereIn("employments.status_id", $allowed_status_ids)->

        when(count($black_lists) > 0, function ($query) use ($black_lists) {
            return $query->whereNotIn("employments.id", array_keys($black_lists));
        })->
        when($search != "", function ($query) use ($search) {
            return $query->where(function ($query) use ($search) {
                $query->where("employments.national_code", "like", "%" . $search . "%")->
                orwhere("employments.mobile", "like", "%" . $search . "%")->
                orwhere("users.firstname", "like", "%" . $search . "%")->
                orwhere("users.lastname", "like", "%" . $search . "%")->
                orwhere("companies.caption", "like", "%" . $search . "%");
            });
        })->
        when($cooperation_type_id != 0, function ($query) use ($cooperation_type_id) {
            return $query->where("employments.cooperation_type_id", $cooperation_type_id);
        })->
        whereNotNull("employments.user_id")->
        orderBy('employments.created_at', 'desc')->
        select('employments.*')->
        paginate();

        $status_option = Option::get("status_in_ids", $status_id, null, $allowed_status_ids);
        $cooperation_type_option = Option::get("cooperation_type", $cooperation_type_id, 0, [1, 11, 3, 31, 61, 6, 21, 2]);
        return view($this->view_path . "index", compact('list', 'search', 'status_option', 'cooperation_type_option',"allow_direct_register"));
    }

    public function view(Employment $employment)
    {

        $allow_delete = false;
        $active_tab = self::get_active_tab($employment);

        $controller_info = self::get_controller_info();
        $special_condition = self::get_special_condition($employment);

        $confirm_info_permission = ConfirmInfoController::checkPermission($employment)["result"];
        $allow_show_upload = false;

        $employment_document_types_for_personal = EmploymentDocumentType::
        where('employment_id', $employment->id)->
        whereIn('receive_document_step_id', [1, 11])->
        get();

        $employment_document_types_for_address = EmploymentDocumentType::where([
            'receive_document_step_id' => 2,
            'employment_id' => $employment->id
        ])->first();

        $allow_user_academic_degree_upload = UserAcademicDegree::join('academic_degree_types', 'academic_degree_types.id', 'user_academic_degrees.academic_degree_type_id')
            ->join('post_document_receive_step_confirms', 'post_document_receive_step_confirms.receive_document_step_id', 'academic_degree_types.receive_document_step_id')
            ->where('user_academic_degrees.user_id', $employment->user_id)
            ->where('post_document_receive_step_confirms.post_id', $employment->post_id)
            ->where('post_document_receive_step_confirms.confirm_type', 2)
            ->select('user_academic_degrees.*', 'academic_degree_types.*', 'post_document_receive_step_confirms.*', 'user_academic_degrees.id as user_academic_degree_id')
            ->get();

        return view($this->view_path . "view", compact('employment', 'employment_document_types_for_address', 'employment_document_types_for_personal', 'allow_show_upload', 'allow_delete', "active_tab", "controller_info", "confirm_info_permission", "special_condition","allow_user_academic_degree_upload"));
    }


    public function letter(Employment $employment)
    {
        WorkMedicineController::WorkMedicineLetter($employment);
    }


    public function view_selection_result(Employment $employment, EmploymentSelection $employment_selection)
    {

        if (!$employment_selection) {
            return back()->withErrors("اطلاعات گزینش نامعتبر است، لطفا یکبار دیگر تلاش کنید");
        }
        $employment_log = EmploymentLog::where([
            "employment_selection_id" => $employment_selection->id,
            "event_id" => 4640004,
            "user_id" => $employment_selection->user_id,
        ])->whereNotNull('message_id')->first();


        return view($this->view_path . "view_selection_result", compact('employment', 'employment_selection', 'employment_log'));
    }

    public static function checkPermissionConditions(Employment $employment, $info = false)
    {

        if ($info != false) {
            foreach ($info["enable_status"] as &$value) {
                $value = DashboardController::$perfix_status_code . $value;
            }

            unset($value);
            if (!in_array($employment->status_id, $info["enable_status"])) {
                return [
                    "result" => false,
                    "error" => "وضعیت درخواست همکاری جهت عملیات نامعتبر است",
                    "error_type" => "for_form_status"
                ];
            }

            $post_user = Auth::user()->posts->first();
            if (!$post_user->checkButtonPermission($info["route"] . "index")) {
                return [
                    "result" => false,
                    "error" => "دسترسی  عملیات برای شما تعریف نشده است",
                ];
            }
        }

        return [
            "result" => true,
        ];

    }


    public static function get_controller_info($type = "")
    {
        // EmploymentProcessSeeder
        $controller_info = [
            "001" => ConfirmInfoController::$info,
            "002" => CoordinationSelectionController::$info,
            "003" => RegistrationOfSelectionResultController::$info,
            "004" => Admin\Supplier\DraftingContractController::$info,
            "005" => Admin\Supplier\DraftingContractInitController::$info,
            "006" => Admin\Supplier\DraftingContractFinalController::$info,
            "007" => Admin\Supplier\RegistrationCostCenterController::$info,
            "008" => DeliveryOfDocumentController::$info,
            "009" => RequestRejectController::$info,
            "010" => FinancialInformationController::$info,
            "011" => InternetAccountController::$info,
            "012" => Admin\Customer\DraftingContractController::$info,
            "013" => Admin\Customer\DraftingContractInitController::$info,
            "014" => Admin\Customer\DraftingContractFinalController::$info,
            "015" => Admin\Customer\RegistrationCostCenterController::$info,
            "016" => Admin\Customer\DeliveryOfDocumentController::$info,
            "017" => Admin\Supplier\DeliveryOfDocumentController::$info,
            "018" => Admin\Contractor\DraftingContractController::$info,
            "019" => Admin\Contractor\DraftingContractInitController::$info,
            "020" => Admin\Contractor\DraftingContractFinalController::$info,
            "021" => Admin\Contractor\RegistrationCostCenterController::$info,
            "022" => Admin\Contractor\DeliveryOfDocumentController::$info,
            "023" => Admin\Customer\ConfirmDraftInformationController::$info,
            "024" => Admin\Contractor\ConfirmDraftInformationController::$info,
            "025" => Admin\Supplier\ConfirmDraftInformationController::$info,
            "026" => Admin\Personal\UpdateDocumentController::$info,
            "027" => Admin\DirectRegisterController::$info,
        ];
        return $controller_info;
    }

    public static function get_special_condition(Employment $employment)
    {
        $employment_selection = $employment->employment_selections()->
        where([
            'priority_number' => $employment->current_priority_number,
            'status_id' => 4640103 // در انتظار انجام
        ])->
        first();
        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $post_ids[] = -1;
        $list = [];
        if ($employment_selection) {
            $list["003"] = RegistrationOfSelectionResultController::get_special_condition($employment_selection, $post_ids);
        }
        $list["005"] = 0;
        $list["006"] = 0;


        $supplier_draft_contract_required_init_confirm = Setting::getIntegerValue("supplier_draft_contract_required_init_confirm");
        $supplier_draft_contract_post_ids_for_init_confirm = Setting::getStringValue("supplier_draft_contract_post_ids_for_init_confirm");
        $supplier_draft_contract_required_final_confirm = Setting::getIntegerValue("supplier_draft_contract_required_final_confirm");
        $supplier_draft_contract_post_ids_for_final_confirm = Setting::getStringValue("supplier_draft_contract_post_ids_for_final_confirm");


        if ($supplier_draft_contract_required_init_confirm && in_array($supplier_draft_contract_post_ids_for_init_confirm, $post_ids)) {
            $list["005"] = 1;
        }

        if ($supplier_draft_contract_required_final_confirm && in_array($supplier_draft_contract_post_ids_for_final_confirm, $post_ids)) {
            $list["006"] = 1;
        }
        return $list;
    }

    public static function get_active_tab(Employment $employment)
    {
        switch ($employment->cooperation_type_id) {
            case 1: //کارمند تمام وقت
            case 11://وکارمند پاره وقت
                $active_tab = "personal_info";
                if ($employment->status_personal_id == 4641401 || $employment->status_personal_id == 4641404) {
                    $active_tab = "personal_info";
                } else {
                    if ($employment->status_address_id == 4641401 && $employment->status_personal_id == 4641402) {
                        $active_tab = "address";
                    } else {
                        if ($employment->status_academic_degree_id == 4641401 && ($employment->status_address_id == 4641402 || $employment->status_address_id == 4641404)) {
                            $active_tab = "academic_degree";
                        } else {
                            if ($employment->status_job_information_id == 4641401 && ($employment->status_academic_degree_id == 4641402 || $employment->status_academic_degree_id == 4641404)) {
                                $active_tab = "job_information";
                            } else {
                                if ($employment->status_educational_course_id == 4641401 && ($employment->status_job_information_id == 4641402 || $employment->status_job_information_id == 4641404)) {
                                    $active_tab = "educational_course";
                                } else {
                                    if ($employment->status_dependent_id == 4641401 && ($employment->status_educational_course_id == 4641402 || $employment->status_educational_course_id == 4641404)) {
                                        $active_tab = "dependent";
                                    }
                                }

                            }

                        }

                    }

                }


                return $active_tab;

                break;

            case 2://پیمانکار
            case 21://نماینده پیمانکار
            case 6://تامین کننده
            case 61://نماینده تامین کننده
            case 3://مشتری
            case 31://نماینده مشتری
                if ($employment->personal_type_id == 1) {
                    $active_tab = "personal_info";
                    if ($employment->status_address_id == 4641401 && $employment->status_personal_id == 4641402) {
                        $active_tab = "address";
                    }
                } else {
                    $active_tab = "personal_info";
                    if ($employment->status_company_id == 4641401 && $employment->status_personal_id == 4641402) {
                        $active_tab = "company_info";
                    } else if ($employment->status_address_id == 4641401 && $employment->status_company_id == 4641402) {
                        $active_tab = "address";
                    }
                }

                return $active_tab;
                break;
        }
    }
}
