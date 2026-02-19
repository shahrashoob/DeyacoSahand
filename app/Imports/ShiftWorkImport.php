<?php

namespace App\Imports;

use App\Models\HR\Shift\NewShiftWorkDay;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Morilog\Jalali\CalendarUtils;

class ShiftWorkImport implements ToCollection {
    /**
     * @param Collection $collection
     */
    var $shift;
    var $year;


    public function collection( Collection $rows ) {
        //
        $cols = [
            "shift_code"                    => 0,
            "shift_work_id"                 => 1,
            "day"                           => 2,
            "work_day_type_id"              => 3,
            "legal_working_hours_in_minute" => 4,
            "description"                   => 5,
            "part_time_count"               => 6,
            "start_datetime1"               => 7,
            "end_datetime1"                 => 8,
            "start_datetime2"               => 9,
            "end_datetime2"                 => 10,
            "start_datetime3"               => 11,
            "end_datetime3"                 => 12,
        ];

        NewShiftWorkDay::where( "id", ">", 0 )->delete();
        $i            = 0;
        $error_format = "";
        foreach ( $rows as $row ) {
            $i ++;
            if ( $i == 1 ) {
                foreach ( $cols as $k => $v ) {
                    if ( ! isset( $row[ $cols[ $k ] ] ) || $row[ $cols[ $k ] ] != $k ) {
                        $error_format = "فرمت فایل به درستی انتخاب نشده است." . "<br/>";
                    }
                }
            }
            if ( $error_format ) {
                $shift_work_day                 = new NewShiftWorkDay();
                $shift_work_day->day            = 1;
                $shift_work_day->shift_id       = $this->shift->id;
                $shift_work_day->shift_caption  = $this->shift->caption;
                $shift_work_day->description    = "";
                $shift_work_day->shift_work_id  = 1;
                $shift_work_day->start_datetime = "";
                $shift_work_day->end_datetime   = "";
                $shift_work_day->message        = $error_format;

                $shift_work_day->save();

                return;
            }
            if ( $i <= 2 ) {
                continue;
            }
            foreach ( $cols as $k => $v ) {
                if ( ! isset( $row[ $v ] ) ) {
                    $row[ $v ] = 0;
                }
            }
            $error_text = "";
            $first_day_in_year = CalendarUtils::toGregorian( $this->year, 1, 1 );

            $shift_code = $row[ $cols["shift_code"] ];
            if ( $shift_code+0 != $this->shift->getCode() +0) {
                $error_text .= "کد شیفت ($shift_code) نامعتبر است." ."<br/>";
            }
            $shift_work_id = $row[ $cols["shift_work_id"] ];
            if ( $shift_work_id > $this->shift->number_of_shift_work ) {
                $error_text .= "گروه شیفت به درستی وارد نشده است." . "<br/>";
            }


            $day = $row[ $cols["day"] ] + 0;
            if ( $day > 366 || $day < 1 ) {
                $error_text .= "شماره روز به درستی وارد نشده است." . "<br/>";
            }

            $start_datetime = [];
            $end_datetime   = [];
            $max_part_time  = $row[ $cols["part_time_count"] ];
            $month          = $this->getMonth( $day );
            $work_day_type_id = $row[ $cols["work_day_type_id"] ];
            $legal_working_hours_in_minute = $row[ $cols["legal_working_hours_in_minute"] ];

            if ( $max_part_time <= 0 ) {
                $datetime                       = Carbon::create( $first_day_in_year[0], $first_day_in_year[1], $first_day_in_year[2], 0, 0 )->addDay( $day - 1 );
                $shift_work_day                 = new NewShiftWorkDay();
                $shift_work_day->row_id         = $i;
                $shift_work_day->day            = $day;
                $shift_work_day->shift_id       = $this->shift->id;
                $shift_work_day->shift_caption  = $this->shift->caption;
                $shift_work_day->description    = $row[ $cols["description"] ] == "0" ? "" : $row[ $cols["description"] ];
                $shift_work_day->shift_work_id  = $shift_work_id ?? "";
                $shift_work_day->work_day_type_id              = $work_day_type_id;
                $shift_work_day->legal_working_hours_in_minute = $legal_working_hours_in_minute;
                $shift_work_day->start_datetime = null;
                $shift_work_day->end_datetime   = null;
                $shift_work_day->datetime       = $datetime;
                $shift_work_day->message        = $error_format;

                $shift_work_day->save();
            }

            for ( $k = 1; $k <= $max_part_time; $k ++ ) {
                $error_datetime[ $k ] = false;
                $start_datetime[ $k ] = explode( ":", $row[ $cols[ "start_datetime" . $k ] ] );
                if ( count( $start_datetime[ $k ] ) != 2 ) {
                    $error_datetime[ $k ] = true;
                    $error_text           .= "زمان شروع " . $k . " به درستی وارد نشده است." . "<br/>";
                }

                $end_datetime[ $k ] = explode( ":", $row[ $cols[ "end_datetime" . $k ] ] );
                if ( count( $end_datetime[ $k ] ) != 2 ) {
                    $error_datetime[ $k ] = true;
                    $error_text           .= "زمان پایان " . $k . " به درستی وارد نشده است." . "<br/>";
                }

            }

            $start_days = [];
            $end_days   = [];


            if ( ! in_array( $work_day_type_id, [ 1, 2, 3 ,4] ) ) {
                $error_text .= "نوع روز کاری " . " به درستی وارد نشده است( 1- روز کاری 2-تعطیل رسمی 3-تعطیل غیر رسمی و 4- جمعه کاری)." . "<br/>";
            }


            if ( $legal_working_hours_in_minute < 0 ) {
                $error_text .= "ساعت کار قانونی  " . $k . " به درستی وارد نشده است." . "<br/>";

            }


            for ( $k = 1; $k <= $max_part_time; $k ++ ) {

                if ( ! $error_datetime[ $k ] ) {


                    // بررسی ساعت 24
                    if ( $start_datetime[ $k ][0] >= 24 ) {
                        $first_day_datetime = Carbon::create( $first_day_in_year[0], $first_day_in_year[1], $first_day_in_year[2], 0, 0 );
                        $start_days[ $k ]   = $first_day_datetime->addDay( $day );
                    } else {
                        $first_day_datetime = Carbon::create( $first_day_in_year[0]+0, $first_day_in_year[1]+0, $first_day_in_year[2]+0, $start_datetime[ $k ][0]+0, $start_datetime[ $k ][1]+0 );
                        $start_days[ $k ]   = $first_day_datetime->addDay( $day - 1 );
                    }


                    // بررسی ساعت 24
                    if ( $end_datetime[ $k ][0] >= 24 ) {
                        $end_day_datetime = Carbon::create( $first_day_in_year[0], $first_day_in_year[1], $first_day_in_year[2], 0, 0 );
                        $end_days[ $k ]   = $end_day_datetime->addDay( $day );
                    } else {
                        $end_day_datetime = Carbon::create( $first_day_in_year[0], $first_day_in_year[1], $first_day_in_year[2], $end_datetime[ $k ][0]+0, $end_datetime[ $k ][1]+0 );
                        $end_days[ $k ]   = $end_day_datetime->addDay( $day - 1 );
                    }


                    if ( $start_days[ $k ]->greaterThan( $end_days[ $k ] ) ) {
                        $error_text .= "پاره وقت  " . $k . " به درستی وارد نشده است. " . "<br/>";
                    }
                }

                $datetime = Carbon::create( $first_day_in_year[0], $first_day_in_year[1], $first_day_in_year[2], 0, 0 )->addDay( $day - 1 );

                $shift_work_day                                = new NewShiftWorkDay();
                $shift_work_day->row_id                        = $i;
                $shift_work_day->day                           = $day;
                $shift_work_day->month                         = $month;
                $shift_work_day->shift_id                      = $this->shift->id;
                $shift_work_day->work_day_type_id              = $work_day_type_id;
                $shift_work_day->legal_working_hours_in_minute = $legal_working_hours_in_minute;
                $shift_work_day->shift_caption                 = $this->shift->caption;
                $shift_work_day->description                   = $row[ $cols["description"] ] == "0" ? "" : $row[ $cols["description"] ];
                $shift_work_day->shift_work_id                 = $shift_work_id ?? "";
                $shift_work_day->start_datetime                = isset( $start_days[ $k ] ) ? $start_days[ $k ] : $row[ $cols[ "start_datetime" . $k ] ];
                $shift_work_day->end_datetime                  = isset( $end_days[ $k ] ) ? $end_days[ $k ] : $row[ $cols[ "end_datetime" . $k ] ];
                $shift_work_day->datetime                      = $datetime;
                $shift_work_day->message                       = $error_text . ( $error_format ?? "" );

                $shift_work_day->save();

                $month = 0; // برای هر پاره وقت در یک روز فقط یک ستون ماه تکمیل می شود.

            }

//            if($max_part_time == 0){
//                $shift_work_day                                = new NewShiftWorkDay();
//                $shift_work_day->row_id                        = $i;
//                $shift_work_day->day                           = $day;
//                $shift_work_day->month                         = $month;
//                $shift_work_day->shift_id                      = $this->shift->id;
//                $shift_work_day->work_day_type_id              = $work_day_type_id;
//                $shift_work_day->legal_working_hours_in_minute = $legal_working_hours_in_minute;
//                $shift_work_day->shift_caption                 = $this->shift->caption;
//                $shift_work_day->description                   = $row[ $cols["description"] ] == "0" ? "" : $row[ $cols["description"] ];
//                $shift_work_day->shift_work_id                 = $shift_work_id ?? "";
//
//                $shift_work_day->datetime                      = $datetime;
//
//                $shift_work_day->save();
//            }

        }
    }

    public function getMonth( $day ) {
        if ( $day <= 31 ) {
            return 1;
        } elseif ( $day <= 62 ) {
            return 2;
        } elseif ( $day <= 93 ) {
            return 3;
        } elseif ( $day <= 124 ) {
            return 4;
        } elseif ( $day <= 155 ) {
            return 5;
        } elseif ( $day <= 186 ) {
            return 6;
        } elseif ( $day <= 216 ) {
            return 7;
        } elseif ( $day <= 246 ) {
            return 8;
        } elseif ( $day <= 276 ) {
            return 9;
        } elseif ( $day <= 306 ) {
            return 10;
        } elseif ( $day <= 336 ) {
            return 11;
        } else {
            return 12;
        }

    }
}
