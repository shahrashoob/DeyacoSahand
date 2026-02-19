<?php

namespace App\Models\HR\User;

use App\Models\Post\PostUser;
use App\Models\Utility\Notification\SMSMessage;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\DataTypes\SMS;
use function PHPUnit\Framework\isNull;

class UserOperation extends Model
{
    use HasFactory;

    protected $table = "user_operations";

    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public static function CalculateFromDailyShiftOperation($worker, $date, $intervals, $classified_absence_from_regular_working_hours)
    {
        // آیا شاغلین مشغول در پست باید مدت زمان کارکرد روزانه را تکمیل نمایند
        $should_complete_the_duration_of_operation =
            PostUser::join("posts", "posts.id", "post_id")->
            where("user_id", $worker->id)->sum("should_complete_the_duration_of_operation");
        $sum_delay_time_for_entry_and_earlier_time_for_exit = 0;


        $operation = new UserOperation;

        $operation->user_id = $worker->id;
        $operation->current_date = $date;
        $operation->present_in_organ = 0; // حاضر در محل کار
        $operation->allowed_earlier_time_for_entry = 0;
        $operation->allowed_delay_time_for_entry = 0;
        $operation->allowed_earlier_time_for_exit = 0;
        $operation->allowed_delay_time_for_exit = 0;
        $operation->allowed_operation = 0; // کارکرد: طبق اطلاعات تامین اجتماعی
        $operation->overtime = 0; // اضافه کارکرد
        $leave = 0; // مرخصی
        $leave_and_entered_in_organ_and_delay_exit_diff = 0; // مدت زمانی مرخصی داشته و داخل سازمان بوده و تاخیر مجاز خروج بوده
        $entered_in_leave_and_allowed_delay_time_for_exit = false; // در بازه مرخصی و تاخیر مجاز خروج به سازمان وارد شده است.
        $operation->mission = 0; //ماموریت
        $legal_mission = 0;
        $interval_mission = 0;
        $all_mission = 0;

        $operation->internal_absence = 0; // غیبت داخلی
        $operation->legal_absence = 0; // غیبت قانونی

        $operation->internal_leave = 0; // مرخصی داخلی
        $operation->legal_leave = 0; // مرخصی قانونی

        //  $operation->legal_entitlement = 0; // مرخضی استحقاقی قانونی

        $operation->leave_type_id = null;


        $operation->morning = 0;
        $operation->afternoon = 0;
        $operation->night = 0;


        $presence_in_legal_time = 0; // حضور در ساعت کار قانونی
        $allowed_earlier_time_for_entry = [];
        $allowed_delay_time_for_entry = [];
        $allowed_earlier_time_for_exit = [];
        $allowed_delay_time_for_exit = [];
        $present_in_organ_in_shift_work_day = [];

        $allowed_earlier_time_for_exit_used = []; // آیا کاربر از تعجیل مجاز خروج استفاده کرده
        $allowed_delay_time_for_exit_used = []; // آیا کاربر از تاخیر مجاز خروج استفاده کرده
        $allowed_earlier_time_for_entry_used = []; // آیا کاربر از تعجیل مجاز ورود استفاده کرده
        $allowed_delay_time_for_entry_used = []; // آیا کاربر از تاخیر مجاز ورود استفاده کرده

        $shift_work_day_sum = 0; // ساعت کار قانونی
        $shift_and_percent_in_organ = 0; // یک متغیر کمکی برای محاسبه غیبت داخلی
        foreach ($intervals as $interval) {

            $diff_in_second = $interval->diff_in_second();

            // ساعت کار قانونی در روز
            if ($interval->legal_working_hours_in_minute > 0) {
                $operation->allowed_operation = $interval->legal_working_hours_in_minute * 60; //ثانیه
            }

            // زمان شیفت کاری
            if ($interval->shift_work_day_id) {
                $shift_work_day_sum += $diff_in_second;
            }

            // کل کارکرد روزانه
            if ($interval->present_in_organ) {
                $operation->present_in_organ += $diff_in_second;
                if ($interval->shift_work_day_id) {
                    $present_in_organ_in_shift_work_day[$interval->shift_work_day_id] = $interval->shift_work_day_id;
                }
                switch ($interval->split_shift_type_group_id) {
                    case 1:
                        $operation->morning += $diff_in_second;
                        break;
                    case 2:
                        $operation->afternoon += $diff_in_second;
                        break;
                    case 3:
                        $operation->night += $diff_in_second;
                        break;
                }
            }

            // جمع کل حضور غیر مجاز
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                !(
                    $interval->present_in_organ_for_other || // جانضین نیست
                    $interval->overtime_id || // اضافه کاری نیست
                    $interval->shift_work_day_id || // شیفت کاری نیست
                    $interval->allowed_earlier_time_for_entry ||
                    $interval->allowed_delay_time_for_entry ||
                    $interval->allowed_earlier_time_for_exit ||
                    $interval->allowed_delay_time_for_exit
                );
            if ($bool) {
                $operation->not_allowed_present_in_organ += $diff_in_second;
                switch ($interval->split_shift_type_group_id) {
                    case 1:
                        $operation->morning -= $diff_in_second;
                        break;
                    case 2:
                        $operation->afternoon -= $diff_in_second;
                        break;
                    case 3:
                        $operation->night -= $diff_in_second;
                        break;
                }
            }

            // جمع کل حضور مجاز
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                (
                    $interval->present_in_organ_for_other || // جانضین
                    $interval->overtime_id || // اضافه کاری
                    $interval->shift_work_day_id || // شیفت کاری
                    $interval->allowed_earlier_time_for_entry ||
                    $interval->allowed_delay_time_for_entry ||
                    $interval->allowed_delay_time_for_exit ||
                    $interval->allowed_earlier_time_for_exit
                );
            if ($bool) {
                $operation->allowed_present_in_organ += $diff_in_second;
            }

            // جمع کل حضور در زمان شیفت کاری
            $bool =
                $interval->shift_work_day_id &&  // شیفت کاری
                (
                    $interval->present_in_organ ||
                    $interval->leave_id ||
                    $interval->mission_id ||
                    $interval->present_in_organ_for_other  // جانضین
                );
            if ($bool) {
                $shift_and_percent_in_organ += $diff_in_second;
            }
            // غیبت داخلی
//            $bool =
//                !$interval->present_in_organ &&  // حضور ندارد
//                !$interval->leave_id &&  // مرخصی ندارد
//                !$interval->mission_id && // ماموریت ندارد
//                !$interval->replacement_id && // جابجایی نیست
//                $interval->shift_work_day_id  // شیفت کاری
//
//
//            ;
//            if ($bool) {
//                $operation->internal_absence += $diff_in_second;
//            }


            // مقدار کارکرد در ساعت کار قانونی
            $bool =
                $interval->present_in_organ &&
                $interval->shift_work_day_id && // شیفت کاری
                $interval->is_legal_operation_hours;
            if ($bool) {

                $presence_in_legal_time += $diff_in_second;
            }

            // جمع تاخیر های ورود و تعجیل های خروج
            $bool = $interval->allowed_delay_time_for_entry + $interval->allowed_delay_time_for_exit;
            if ($bool) {
                $sum_delay_time_for_entry_and_earlier_time_for_exit += $diff_in_second;
            }

            // مرخصی قانونی
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->shift_work_day_id &&
                $interval->leave_id && // مرخصی
                !$interval->mission_id &&  // ماموریت (اولویت ماموریت بالاتر است)
                $interval->is_legal_operation_hours
//                &&
//                !$interval->present_in_organ_for_other && // جانضین نیست
//                !$interval->overtime_id  // اضافه کاری نیست

            ;
            if ($bool) {

                $operation->legal_leave += $diff_in_second;
                // مقدار دهی انواع مرخصی
                $operation->leave_type_id = $interval->leave_id;
            }
            // مرخصی داخلی
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->shift_work_day_id &&
                $interval->leave_id && // مرخصی
                !$interval->mission_id &&  // ماموریت (اولویت ماموریت بالاتر است)
                !$interval->is_legal_operation_hours
//                &&
//                !$interval->present_in_organ_for_other && // جانضین نیست
//                !$interval->overtime_id  // اضافه کاری نیست

            ;
            if ($bool) {

                $operation->internal_leave += $diff_in_second;
                // مقدار دهی انواع مرخصی
                $operation->leave_type_id = $interval->leave_id;
            }


            // // مدت زمانی مرخصی داشته و خارج از سازمان بوده و تاخیر مجاز خروج بوده
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                $interval->shift_work_day_id &&
                $interval->leave_id  // مرخصی
                &&
                !$interval->present_in_organ_for_other && // جانضین نیست
                !$interval->overtime_id &&  // اضافه کاری نیست
                $interval->allowed_delay_time_for_exit;
            // اگر بازه فوق وجود نداشت (یعنی فرد در زمان تاخیر مجاز وارد نشده بود)
            // مقدار بازه تاخیر مجاز خروج را از بازه کم می کنیم.
            if ($bool) {
                $entered_in_leave_and_allowed_delay_time_for_exit = true;
            }

            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->shift_work_day_id &&
                $interval->leave_id  // مرخصی
                &&
                !$interval->present_in_organ_for_other && // جانضین نیست
                !$interval->overtime_id &&  // اضافه کاری نیست
                $interval->allowed_delay_time_for_exit;
            if ($bool) {
                $leave_and_entered_in_organ_and_delay_exit_diff += $diff_in_second;
            }


            // ماموریت

            /*********** ماموریت در زمان شیفت ******/
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->mission_id &&  // ماموریت
                $interval->is_legal_operation_hours && // زمان ساعت قانونی
                (
                    !$interval->allowed_earlier_time_for_entry ||
                    ($interval->allowed_earlier_time_for_entry && $operation->present_in_organ > 0)
                ) && // اگر تعجیل مجاز است حتما قبلش در سازمان حضور داشته باشند
                (
                    !$interval->allowed_delay_time_for_exit ||
                    ($interval->allowed_delay_time_for_exit && $interval->shift_work_day_id > 0)
                );
            if ($bool) {
                $legal_mission += $diff_in_second;
                $operation->mission += $diff_in_second;

            }

            /******** ماموریت در زمان خارج از شیفت کاری **********/
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->mission_id &&  // ماموریت
                !$interval->is_legal_operation_hours && // زمان ساعت قانونی
                (
                    !$interval->allowed_earlier_time_for_entry ||
                    ($interval->allowed_earlier_time_for_entry && $operation->present_in_organ > 0)
                ) && // اگر تعجیل مجاز است حتما قبلش در سازمان حضور داشته باشند
                (
                    !$interval->allowed_delay_time_for_exit ||
                    ($interval->allowed_delay_time_for_exit && $interval->shift_work_day_id > 0)
                );
            if ($bool) {
                $interval_mission += $diff_in_second;
                $operation->mission += $diff_in_second;

            }

            /******************* کل خالص ماموریت*******************/
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->mission_id &&  // ماموریت
                $interval->mission_id != 1;
            if ($bool) {
                $all_mission += $diff_in_second;


            }


            //تعجیل مجاز در ورود به سازمان (شناسه پست)
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                $interval->allowed_earlier_time_for_entry &&
                !$interval->leave_id &&  // مرخصی
                !$interval->mission_id  // ماموریت
            ;


            if ($bool) {
                if (!isset($allowed_earlier_time_for_entry[$interval->allowed_earlier_time_for_entry])) {
                    $allowed_earlier_time_for_entry[$interval->allowed_earlier_time_for_entry] = 0;
                }
                $allowed_earlier_time_for_entry[$interval->allowed_earlier_time_for_entry] += $diff_in_second;
            }
            //  از تعجیل مجاز ورود استفاده کرده؟
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->allowed_earlier_time_for_entry &&
                $interval->leave_id;  // مرخصی;

            if ($bool) {
                if (!isset($allowed_earlier_time_for_entry_used[$interval->allowed_earlier_time_for_entry])) {
                    $allowed_earlier_time_for_entry_used[$interval->allowed_earlier_time_for_entry] = 0;
                }
                $allowed_earlier_time_for_entry_used[$interval->allowed_earlier_time_for_entry] += $diff_in_second;
            }
            /******************************************************************************************/

            //تاخیر مجاز برای ورود به سازمان (شناسه پست)
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                !$interval->leave_id &&  // مرخصی ندارد
                !$interval->mission_id && // ماموریت ندارد
                !$interval->replacement_id && // جانشین نیست
                $interval->allowed_delay_time_for_entry;


            if ($bool) {
                if (!isset($allowed_delay_time_for_entry[$interval->allowed_delay_time_for_entry])) {
                    $allowed_delay_time_for_entry[$interval->allowed_delay_time_for_entry] = 0;
                }
                $allowed_delay_time_for_entry[$interval->allowed_delay_time_for_entry] += $diff_in_second;
            }
            //  از تاخیر مجاز ورود استفاده کرده؟
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                !$interval->leave_id &&  // مرخصی ندارد
                !$interval->mission_id && // ماموریت ندارد
                !$interval->replacement_id && // جانشین نیست
                $interval->allowed_delay_time_for_entry;

            if ($bool) {
                if (!isset($allowed_delay_time_for_entry_used[$interval->allowed_delay_time_for_entry])) {
                    $allowed_delay_time_for_entry_used[$interval->allowed_delay_time_for_entry] = 0;
                }
                $allowed_delay_time_for_entry_used[$interval->allowed_delay_time_for_entry] += $diff_in_second;
            }

            /**********************************************************/
            // تعجیل مجاز برای خروج به سازمان (شناسه پست)
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                !$interval->leave_id &&  // مرخصی ندارد
                !$interval->mission_id && // ماموریت ندارد
                !$interval->replacement_id && // جانشین نیست
                $interval->allowed_earlier_time_for_exit;

            if ($bool) {
                if (!isset($allowed_earlier_time_for_exit[$interval->allowed_earlier_time_for_exit])) {
                    $allowed_earlier_time_for_exit[$interval->allowed_earlier_time_for_exit] = 0;
                }
                $allowed_earlier_time_for_exit[$interval->allowed_earlier_time_for_exit] += $diff_in_second;
            }

            //  از تعجیل مجاز خروج استفاده کرده؟
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                !$interval->leave_id &&  // مرخصی ندارد
                !$interval->mission_id && // ماموریت ندارد
                !$interval->replacement_id && // جانشین نیست
                $interval->allowed_earlier_time_for_exit;

            if ($bool) {
                if (!isset($allowed_earlier_time_for_exit_used[$interval->allowed_earlier_time_for_exit])) {
                    $allowed_earlier_time_for_exit_used[$interval->allowed_earlier_time_for_exit] = 0;
                }
                $allowed_earlier_time_for_exit_used[$interval->allowed_earlier_time_for_exit] += $diff_in_second;
            }
            /**********************************************************/


            //تاخیر مجاز برای خروج از سازمان (شناسه پست)
            $bool =
                $interval->present_in_organ &&  // حضور دارد
                !$interval->leave_id &&  // مرخصی ندارد
                !$interval->mission_id && // ماموریت ندارد
                !$interval->replacement_id && // جانشین نیست
                $interval->allowed_delay_time_for_exit;

            if ($bool) {
                if (!isset($allowed_delay_time_for_exit[$interval->allowed_delay_time_for_exit])) {
                    $allowed_delay_time_for_exit[$interval->allowed_delay_time_for_exit] = 0;
                }
                $allowed_delay_time_for_exit[$interval->allowed_delay_time_for_exit] += $diff_in_second;
            }

            //  از تاخیر مجاز خروج استفاده کرده؟
            $bool =
                !$interval->present_in_organ &&  // حضور ندارد
                $interval->allowed_delay_time_for_exit;

            if ($bool) {
                if (!isset($allowed_delay_time_for_exit_used[$interval->allowed_delay_time_for_exit])) {
                    $allowed_delay_time_for_exit_used[$interval->allowed_delay_time_for_exit] = 0;
                }
                $allowed_delay_time_for_exit_used[$interval->allowed_delay_time_for_exit] += $diff_in_second;
            }
            /**********************************************************/

        }

        // اگر در بازه مرخصی و تاخیر مجاز به سازمان وارد نشده است، مقدار تاخیر مجاز را از مرخصی کم می کنیم.
//        if (!$entered_in_leave_and_allowed_delay_time_for_exit) {
//            $leave -= $leave_and_entered_in_organ_and_delay_exit_diff;
//        }

        // به دلیل تغییر الگوریتم حذف شد.
//        if ($operation->allowed_present_in_organ - $operation->allowed_operation > 0) {
//            $operation->overtime = $operation->allowed_present_in_organ - $operation->allowed_operation;
//        }

        $allowed_delay_time_for_entry_used_sum = 0; // جمع تاخیرهای ورود که استفاده کرده
        $allowed_earlier_time_for_exit_used_sum = 0; // جمع تعجیل های ورود که استفاده کرده

        $invalid_internal_absence_from_input_output = 0; // تاخیر ورود / تعجیل خروج غیرمجاز

        // در صورتی تاخیر های ورود و خروج را در نظر می گیریم که طرف در آن بازه در سازمان حضور داشته باشد.
        // برای تاخیر ورود و تعجیل خروج باید حتما فرد در بازه مجاز وارد/خارج شده باشد.
        foreach ($present_in_organ_in_shift_work_day as $shift_work_day_id) {
            if (
                isset($allowed_earlier_time_for_entry[$shift_work_day_id])
            ) {
                $operation->allowed_earlier_time_for_entry += $allowed_earlier_time_for_entry[$shift_work_day_id];
            }


            // از تاخیر ورود به سازمان مجاز بوده است
            if (
                isset($allowed_delay_time_for_entry[$shift_work_day_id]) &&
                isset($allowed_delay_time_for_entry_used[$shift_work_day_id])
            ) {
                $operation->allowed_delay_time_for_entry += $allowed_delay_time_for_entry[$shift_work_day_id];
            }
            // تاخیر ورود به سازمان غیر مجاز بوده => غیبت داخلی
            if (
                isset($allowed_delay_time_for_entry[$shift_work_day_id]) &&
                !isset($allowed_delay_time_for_entry_used[$shift_work_day_id])
            ) {
                $invalid_internal_absence_from_input_output += $allowed_delay_time_for_entry[$shift_work_day_id];
            }

            if (
                isset($allowed_delay_time_for_entry_used[$shift_work_day_id])
            ) {
                $allowed_delay_time_for_entry_used_sum += $allowed_delay_time_for_entry_used[$shift_work_day_id];
            }

            // تعجیل خروج مجاز بوده
            if (
                isset($allowed_earlier_time_for_exit[$shift_work_day_id]) &&
                isset($allowed_earlier_time_for_exit_used[$shift_work_day_id])
            ) {
                $operation->allowed_earlier_time_for_exit += $allowed_earlier_time_for_exit[$shift_work_day_id];
            }
            // تعجیل خروج غیر مجاز بوده
            if (
                isset($allowed_earlier_time_for_exit[$shift_work_day_id]) &&
                !isset($allowed_earlier_time_for_exit_used[$shift_work_day_id])
            ) {
                $invalid_internal_absence_from_input_output += $allowed_earlier_time_for_exit[$shift_work_day_id];
            }
            if (
                isset($allowed_earlier_time_for_exit_used[$shift_work_day_id])
            ) {
                $allowed_earlier_time_for_exit_used_sum += $allowed_earlier_time_for_exit_used[$shift_work_day_id];
            }


            if (
                isset($allowed_delay_time_for_exit[$shift_work_day_id])
            ) {
                $operation->allowed_delay_time_for_exit += $allowed_delay_time_for_exit[$shift_work_day_id];
            }
        }


        // هرچی در زمان کارکرد روزانه سر کار حاضر بوده
        $operation->normal_operation = $presence_in_legal_time;
        if ($operation->allowed_present_in_organ > $operation->allowed_operation) {
            // اگر حضور مجاز فرد بیش از 7:20 دقیقه بوده و تاخیر / تعجیل مجاز داشته، مقدار کارکرد عادی را 7:20 در نظر می گیریم.
            $operation->normal_operation = $operation->allowed_operation - $operation->legal_leave - $legal_mission;
        }
        // اگر حضور مجاز فرد بیشتر از کارکرد عادی باشد و کارکرد عادی از 7:20 دقیقه کمتر باشد،
//        if ($operation->allowed_present_in_organ > $operation->normal_operation && $operation->normal_operation < $operation->allowed_operation) {
//            $sum_legal_leave_mission = $operation->legal_leave - $legal_mission;
//            $operation->normal_operation = (
//                $sum_legal_leave_mission > 0 ? $operation->allowed_operation : $operation->allowed_present_in_organ
//                ) - $sum_legal_leave_mission;
//        }

        $internal_absence_from_input_output = 0; // غیبت داخلی که با توجه به تعجیل / تاخیر مجاز به دست آمده


        // کل غیبت محاسبه می شود.
        $has_allowed_delay_input =
            $allowed_delay_time_for_entry_used_sum != 0
            ||
            ($allowed_delay_time_for_entry_used_sum == 0 && $operation->allowed_earlier_time_for_entry != 0);
        $has_allowed_earlier_output =
            $allowed_earlier_time_for_exit_used_sum != 0
            ||
            ($allowed_earlier_time_for_exit_used_sum == 0 && $operation->allowed_delay_time_for_exit != 0);

        // در صورتی غیبت محاسبه نمی شود که در تاخیر ورود و تعجیل خروج، ورود/خروج انجام شده باشد.
        // چون ما نمی دانیم دقیفا کجا غیبت کرده، مقدار غیبت را از مقدار تاخیر/تعجیل مجاز کم می کنیم.
        if ($has_allowed_delay_input) {

            $internal_absence_from_input_output += $operation->allowed_delay_time_for_entry  // تاخیر ورودی که استفاده کرده
            ;

        } else {
//            $invalid_internal_absence_from_input_output += $operation->allowed_delay_time_for_entry;
//            // اگر تاخیر، تعجیل ها در بازه مجاز نبوده، آنها را صفر می کنیم.
//            //$operation->allowed_delay_time_for_entry = 0;
        }


        if ($has_allowed_earlier_output) {

            $internal_absence_from_input_output += $operation->allowed_earlier_time_for_exit // تعجیل خروجی که استفاده کرده

            ;
        } else {
            // $invalid_internal_absence_from_input_output += $operation->allowed_earlier_time_for_exit;
            // اگر تاخیر، تعجیل ها در بازه مجاز نبوده، آنها را صفر می کنیم.
            //$operation->allowed_earlier_time_for_exit = 0;
        }

// محاسبه اضافه کاری
        // اگر ماموریت هم خارج از ساعت کار قانونی رفته باشد، به عنوان اضافه کار محاسبه می کنیم.
        if ($operation->allowed_present_in_organ + $interval_mission > $operation->normal_operation && $operation->normal_operation + $operation->legal_leave + $interval_mission + $legal_mission >= $operation->allowed_operation) {
            $operation->overtime = $operation->allowed_present_in_organ + $interval_mission - $operation->normal_operation;

            if ($operation->overtime < 0) {
                $operation->overtime = 0;
            }
        }

        // اگر تنظیمات "آیا غیبت از ساعت کار عادی تفکیک شود؟" بله است
        if ($classified_absence_from_regular_working_hours) {

            //             ساعت کارکرد عادی <= ساعت کار در شیفت
            //             اضافه کار= کل ساعت کار مجاز - کارکرد عادی
            $operation->normal_operation = $presence_in_legal_time;
            $operation->overtime = $operation->allowed_present_in_organ - $operation->normal_operation;

            // ممکن است فرد ساعت کار فانونی را پرکرده باشد ولی با تاخیر/تغجیل مجاز، اگر اینطوری باشد نباید غیبت ثبت شود و باید تاخیر تاجیل مجاز او هم لحاظ شود.
            if ($operation->normal_operation < $operation->allowed_operation && $operation->overtime > 0 && $internal_absence_from_input_output > 0) {
                $operation->normal_operation = min($operation->allowed_operation, $operation->normal_operation + $internal_absence_from_input_output);
                $operation->overtime = $operation->allowed_present_in_organ - $operation->normal_operation;

                $operation->overtime = max(0, $operation->overtime);
            }

        }
        // غیبت قانونی
        // اگر جمع حضور مجاز از ساعت کار قانونی کمتر شد، غیبت در نظر می گیریم.
        $operation->legal_absence =
            $operation->allowed_operation - //ساعت کار قانونی
            (
                $operation->normal_operation + // حضور مجاز
                $operation->legal_leave + // مرخصی
                $legal_mission //ماموریت
            );

        if ($operation->legal_absence < 0) {
            $operation->legal_absence = 0;
        }


        // اگر شاغلین باید مدت زمان کارکرد روزانه را تکمیل نمایند
        if ($should_complete_the_duration_of_operation) {
// غیبت قانونی را از غیبت داخلی کم می کنیم اگر کمتر از 1- دقیقه بود، پیامک خطا می دهیم در غیر این صورت مقدار غیبت را صفر می کنیم.
//غیبت داخلی: زمان شیفت کاری - غیبت قانونی
            $operation->internal_absence = $shift_work_day_sum - // ساعت کارکرد داخلی
                (
                    $operation->allowed_present_in_organ + // حضور مجاز
                    $operation->internal_leave + $operation->legal_leave + // مرخصی
                    $operation->mission //ماموریت
                    + $operation->legal_absence +
                    $operation->overtime
                ); // غیبت قانونی;

        } else {
            // غیبت داخلی می شود مقدار تاخیر/تعجیل غیرمجاز
            $operation->internal_absence = $shift_work_day_sum - // ساعت کارکرد داخلی
                $shift_and_percent_in_organ - $operation->legal_absence - $internal_absence_from_input_output;
        }

        if ($operation->internal_absence < 0) {
            $operation->internal_absence = 0;
        }
//
//


        $operation->mission = $legal_mission;
        // کارکرد عادی
//    delete    $operation->normal_operation =min($operation->allowed_present_in_organ,$operation->allowed_operation);

        return $operation;
    }

    public
    function get_date($format = "%A, %d %B ")
    {
        return jdate(Carbon::parse($this->current_date)->timestamp)->format($format);
    }

    public
    function get_time($second)
    {
        if ($second == 0) {
            return "";
        }
        $list = $this->hours_minute($second);
        return $list["hours"] . ":" . ($list["minute"] < 10 ? "0" . $list["minute"] : $list["minute"]);
    }

    public
    function hours_minute($second)
    {
        $hours = (int)($second / 3600);
        $minute = (int)(($second - $hours * 3600) / 60);

        return ["hours" => $hours, "minute" => $minute, "second" => $second];
    }

}
