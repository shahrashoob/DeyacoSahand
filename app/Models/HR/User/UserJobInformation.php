<?php

namespace App\Models\HR\User;

use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserJobInformation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'start_date_of_work',
        'end_date_of_work',
        'post_caption',
        'company_name_of_work',
        'address_of_work',
        'identifier_name',
        'identification_number',
    ];
    protected $table = 'user_job_informations';

    public function get_start_date_of_work()
    {
        return jdate(Carbon::parse($this->start_date_of_work)->timestamp)->format('Y/m/d');

    }
    public function get_end_date_of_work()
    {
        return jdate(Carbon::parse($this->end_date_of_work)->timestamp)->format('Y/m/d');

    }
    public function get_employment_document_type(){

        return EmploymentDocumentType::where([
            'receive_document_step_id'=> 8,
            'other_id'=>$this->id,
        ])->first();

    }
    public static function CreateJobInformationInIc(Employment $employment, $token)
    {

        $job_informations = $employment->worker->user_job_informations()->get()->toArray();

        $settings = [
            'base_uri' => env('IC_URL').'/api/',
            'headers' => [
            ],

            'query' => [
                'personal_id' => $employment->worker->personal_id_in_ic_system,
                'list' => $job_informations,
                'token' => $token,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "personal_job_information"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }
}
