<?php

namespace Database\Seeders\OldSeeder;

use App\Models\HR\Agent\Agent;
use App\Models\HR\Employment\Employment;
use App\Models\HR\User\UserAddress;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use App\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    private $data = [

        ["lastname" => "متفرقه", "national_code" => "200000001","gender_id"=>1, "firstname" => "مشتری", "user_type_id" => 2, "name" => "public_customer", "email" => "public@costomer", "password" => "@public_customer", "cooperation_type_id" => 3],


    ];
    private $table = 'customers';

    public function run()
    {
        foreach ($this->data as $item) {
            if (!User::where("email", $item["email"])->first()) {


                $user_suppler = Worker::firstOrCreate(["email" => "public@customer"], $item);

                $address = Address::firstOrCreate(
                    [
                        "postal_code" => "200000000000"
                    ],
                    [
                        "country_id" => 112,
                        "province_id" => 31,
                        "phone" => "021123456789",
                        "address" => "آدرس مشتری متفرقه",
                        "postal_code" => "200000000000",
                        "city_name" => "تهران",
                        "mobile" => "913000001"
                    ]

                );
                UserAddress::firstOrCreate(["user_id" => $user_suppler->id, "address_id" => $address->id, "is_default" => 1]);

                $agent = Agent::firstOrCreate([
                    'user_id' => $user_suppler->id,
                    'company_id' => null,
                ],
                    ['user_id' => $user_suppler->id]);

                Employment::create([
                    "user_id" => $user_suppler->id,
                    "status_id" => 4640122,
                    'current_priority_number' => 0,
                    "cooperation_type_id" => 3,
                    "personal_type_id" => 1,
                    "nationality_id" => 1,
                    "national_code" => $item["national_code"],
                    "country_id" => 112,
                    "mobile_country_id" => 112,
                    "mobile" => "91311111111",
                    'status_personal_id' => 4641402,
                    "status_address_id" => 4641402,
                    "status_academic_degree_id" => 4641402,
                    "status_job_information_id" => 4641402,
                    "status_educational_course_id" => 4641402,
                    "status_upload_document_id" => 4641402,
                    'status_dependent_id' => 4641402,
                    'contract_register_id' => 4641402,
                    'status_company_id' => 4641402

                ]);
            }
        }
    }
}
