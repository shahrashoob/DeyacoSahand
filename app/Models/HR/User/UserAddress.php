<?php

namespace App\Models\HR\User;

use App\Models\HR\Employment\Employment;
use App\Models\HR\Employment\EmploymentDocumentType;
use App\Models\Utility\Address\Address;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    protected $fillable = ["user_id", "address_id", "is_default", 'company_id'];

    protected $table = 'user_address';

    public function address()
    {

        return $this->belongsTo(Address::class);
    }

    public function worker()
    {

        return $this->belongsTo(Worker::class);
    }

    public function get_employment_document_type()
    {


        return EmploymentDocumentType::where([
            'receive_document_step_id' => 2,
        ])->first();

    }

    public static function CreateAddressInIc(Employment $employment, $token)
    {
        if ($employment->personal_type_id == 1) {
            if ($employment->worker->user_address()->count() == 0) {
                return false;
            }

            $address = $employment->worker->user_address()->first()->address;
        } else {
            if ($employment->company->user_address()->count() == 0) {
                return false;
            }

            $address = $employment->company->user_address()->first()->address;
        }

        $settings = [
            'base_uri' => env('IC_URL').'/api/',
            'headers' => [
            ],
            'query' => [
                'personal_id' => ($employment->personal_type_id == 1) ? $employment->worker->personal_id_in_ic_system : null,
                'company_id' => ($employment->personal_type_id == 2) ? $employment->company->company_id_in_ic_system : null,
                'country_id' => $address->country_id,
                'city_name' => $address->city_name,
                'province_id' => $address->province_id,
                'postal_code' => $address->postal_code,
                'phone' => $address->phone,
                'mobile' => $address->mobile,
                'address' => $address->address,
                'state_id' => 0,
                'city_id' => 0,
                'token' => $token,
                'personal_type_id' => $employment->personal_type_id,
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "personal_address"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

}