<?php

namespace Database\Seeders\Utility\Contract;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContractKeywordSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    private $data = [
        //

        ["id" => 1, "caption" => "نام و نام خانودگی(طرف اول)", "keyword" => "Name_A"],
        ["id" => 2, "caption" => "نام و نام خانودگی(طرف دوم)", "keyword" => "Name_B"],
        ["id" => 3, "caption" => "فرزند(طرف اول)", "keyword" => "Father_Name_A"],
        ["id" => 4, "caption" => "فرزند(طرف دوم)", "keyword" => "Father_Name_B"],
        ["id" => 5, "caption" => "شماره شناسنامه(طرف اول)", "keyword" => "Birth_Certificate_Number_A"],
        ["id" => 6, "caption" => "شماره شناسنامه(طرف دوم)", "keyword" => "Birth_Certificate_Number_B"],
        ["id" => 7, "caption" => "نشانی(طرف اول)", "keyword" => "Address_A"],
        ["id" => 8, "caption" => "نشانی(طرف دوم)", "keyword" => "Address_B"],
        ["id" => 9, "caption" => "تاریخ تولد(طرف اول)", "keyword" => "Date_A"],
        ["id" => 10, "caption" => "تاریخ تولد(طرف دوم)", "keyword" => "Date_B"],
        ["id" => 11, "caption" => "شماره همراه(طرف اول)", "keyword" => "Mobile_A"],
        ["id" => 12, "caption" => "شماره همراه(طرف دوم)", "keyword" => "Mobile_B"],
        ["id" => 13, "caption" => "مزد روزانه", "keyword" => "Salary_Date"],
        ["id" => 14, "caption" => "حقوق ماهانه", "keyword" => "Salary_Month"],
        ["id" => 15, "caption" => "حق شاغل", "keyword" => "Right_Work"],
        ["id" => 16, "caption" => "تعداد نسخه قرارداد", "keyword" => "Number_Of_Contract"],
        ["id" => 17, "caption" => "تاریخ شروع قراداد", "keyword" => "Start_Date"],
        ["id" => 18, "caption" => "تاریخ پایان قرارداد", "keyword" => "End_Date"],
        ["id" => 21, "caption" => "نام شرکت(طرف اول)", "keyword" => "Company_name_A"],
        ["id" => 22, "caption" => "نام شرکت(طرف دوم)", "keyword" => "Company_name_B"],
        ["id" => 23, "caption" => "شماره ملی(طرف اول)", "keyword" => "National_Code_A"],
        ["id" => 24, "caption" => "شماره ملی(طرف دوم)", "keyword" => "National_Code_B"],
        ["id" => 25, "caption" => "میزان تحصیلات", "keyword" => "Academic_Degree_A"],
//        ["id" => 26, "caption" => "میزان تحصیلات(طرف دوم)", "keyword" => "Academic_Degree_B"],
        ["id" => 27, "caption" => "شناسه ملی(طرف اول)", "keyword" => "Company_National_Code_A"],
        ["id" => 28, "caption" => "شناسه ملی(طرف دوم)", "keyword" => "Company_National_Code_B"],
        ["id" => 29, "caption" => "به نمایندگی(طرف اول)", "keyword" => "Representation_A"],
        ["id" => 30, "caption" => "به نمایندگی(طرف دوم)", "keyword" => "Representation_B"],
        ["id" => 31, "caption" => "پست سازمانی(طرف اول)", "keyword" => "Post_A"],
        ["id" =>32, "caption" => "پست سازمانی(طرف دوم)", "keyword" => "Post_B"],
        ["id" =>33, "caption" => " کار یا حرفه یا حجم کار یا وظیفه ای که کارگر به آن اشتغال می یابد", "keyword" => "Duties_A"],
        ["id" =>34, "caption" => " شماره حساب", "keyword" => "Account_Number"],
        ["id" =>35, "caption" => "شعبه بانک", "keyword" => "Bank_Branch"],
        ["id" =>36, "caption" => "نام بانک", "keyword" => "Bank_Name"],
        ["id" =>37, "caption" => "مدت زمان اعلام فسخ قراداد", "keyword" => "Number_Of_Days_Before_Termination_Of_Contract"],
        ["id" =>38, "caption" => "نام کامل فرد/شرکت (طرف اول) ", "keyword" => "FullName_A"],
        ["id" =>39, "caption" => "نام کامل فرد/شرکت (طرف دوم) ", "keyword" => "FullName_B"],
        ["id" => 40, "caption" => "شماره ملی/شناسه ملی(طرف اول)", "keyword" => "National_Code_Full_A"],
        ["id" => 41, "caption" => "کد ملی/شناسه ملی(طرف دوم)", "keyword" => "National_Code_Full_B"],
        ["id" => 42, "caption" => "مدت قرارداد", "keyword" => "ContractPeriod"],
        ["id" => 43, "caption" => "مبلغ ضمانت قرارداد (وثیقه)", "keyword" => "GuaranteeAmount"],

    ];
    private $table = 'contract_keywords';

    public function run()
    {
        DB::table($this->table)->delete();
        foreach ($this->data as $item) {

            if (!DB::table($this->table)->
            where("id", $item["id"])->first()) {
                DB::table($this->table)->insert($item);
            } else {
                DB::table($this->table)->where("id", $item["id"])->update($item);
            }

        }
    }
}
