<?php

namespace App\Models\HR\User;

use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\HR\Personal\AcademicDegreeType;
use App\Models\HR\Personal\DependentType;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDependent extends Model
{
    use HasFactory;
    protected $table = 'user_dependents';
    protected $fillable = [
        'user_id','dependent_type_id','first_name','last_name','national_code','date_of_birth'
    ];
    public function dependent_type()
    {
        return $this->belongsTo(DependentType::class);
    }
    public function fullname()
    {
        $text= $this->first_name . " " . $this->last_name;
        return $text;
    }
    public function get_date_of_birth()
    {
        if ($this->date_of_birth)
            return jdate(Carbon::parse($this->date_of_birth)->timestamp)->format('Y/m/d');

    }
    public function get_employment_document_type(){

        return EmploymentDocumentType::where([
            'receive_document_step_id'=> 12,
            'other_id'=>$this->id,
        ])->first();

    }
    public static function CreateDependentInIc(Employment $employment, $token)
    {
        if ($employment->worker->user_dependents()->count() == 0) {
            return false;
        }
        $user_dependent = $employment->worker->user_dependents()->get()->toArray();

        $settings = [
            'base_uri' => env('IC_URL').'/api/',
            'headers' => [
            ],

            'query' => [
                'personal_id' => $employment->worker->personal_id_in_ic_system,
                'list' =>  $user_dependent,
                'token' => $token,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "personal_dependent"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }
}
