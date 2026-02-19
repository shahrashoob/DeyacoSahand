<?php

namespace App\Models\HR\Company;

use App\Models\HR\User\UserAddress;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{

    protected $fillable = [
        'company_id_in_ic_system',
        'user_id',
        'caption',
        'register_code',
        'national_code',
    ];
    protected $table = "companies";


    public function user_address()
    {
        return $this->hasMany(UserAddress::class, 'company_id');
    }
    public function worker()
    {
        return $this->belongsTo(Worker::class, 'user_id');
    }
    public static function CreateCompanyInIc($employment, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL').'/api/',
            'headers' => [
            ],
            'query' => [
                'personal_id' => $employment->worker->personal_id_in_ic_system,
                'national_code' => $employment->company->national_code,
                'register_code'=>$employment->company->register_code,
                'caption'=>$employment->company->caption,
                'token'=>$token,
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "create-company"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

}
