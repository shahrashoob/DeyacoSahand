<?php

namespace App\Models\HR\User;

use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserEducationalCourse extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'course_name',
        'name_of_institution',
        'start_date',
        'end_date',
        'duration',
    ];
    protected $table = 'user_educational_courses';


    public function get_start_date()
    {
        return jdate(Carbon::parse($this->start_date)->timestamp)->format('Y/m/d');

    }

    public function get_end_date()
    {
        return jdate(Carbon::parse($this->end_date)->timestamp)->format('Y/m/d');

    }
    public function get_employment_document_type(){


        return EmploymentDocumentType::where([
            'receive_document_step_id'=> 9,
            'other_id'=>$this->id,
        ])->first();

    }

    public static function CreateEducationalCourseInIc(Employment $employment, $token)
    {

        $educational_courses = $employment->worker->user_educational_courses()->get()->toArray();

        $settings = [
            'base_uri' => env('IC_URL').'/api/',
            'headers' => [
            ],

            'query' => [
                'personal_id' => $employment->worker->personal_id_in_ic_system,
                'list' => $educational_courses,
                'token' => $token,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "personal_educational_course"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }


}
