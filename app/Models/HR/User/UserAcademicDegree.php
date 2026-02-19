<?php

namespace App\Models\HR\User;

use App\Models\File\File;
use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Personal\AcademicDegree;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\FeildOfAcademicDegree;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAcademicDegree extends Model
{
    protected $fillable = [
        "user_id", 'academic_degree_type_id', 'name_of_academic_degree',
        'academic_degree_file_id', 'average', 'feild_of_academic_degree_id',
        "start_date",
        "end_date"
    ];

    protected $table = 'user_academic_degrees';

    public function academic_degree_type()
    {
        return $this->belongsTo(AcademicDegreeType::class, 'academic_degree_type_id');
    }

    public function feild_of_academic_degree()
    {
        return $this->belongsTo(FeildOfAcademicDegree::class);
    }
    public function worker()
    {

        return $this->belongsTo(Worker::class, 'user_id');
    }

    public function academic_degree_file()
    {

        return $this->belongsTo(File::class);
    }

    public function get_start_date()
    {
        if ($this->start_date)
            return jdate(Carbon::parse($this->start_date)->timestamp)->format('Y/m/d');

    }

    public function get_employment_document_type(){

            $step_id = $this->academic_degree_type->receive_document_step_id;
           return EmploymentDocumentType::where([
                'receive_document_step_id'=> $step_id,
                'other_id'=>$this->id,
            ])->first();

    }
    public function get_end_date()
    {
        if ($this->end_date)
        return jdate(Carbon::parse($this->end_date)->timestamp)->format('Y/m/d');

    }
    public static function CreateAcademicDegreeInIc(Employment $employment, $token)
    {
        if ($employment->worker->user_academic_degrees()->count() == 0) {
            return false;
        }
        $academic_degree = $employment->worker->user_academic_degrees()->get()->toArray();

        $settings = [
            'base_uri' => env('IC_URL').'/api/',
            'headers' => [
            ],

            'query' => [
                'personal_id' => $employment->worker->personal_id_in_ic_system,
                'list' => $academic_degree,
                'token' => $token,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "personal_academic_degree"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }
}
