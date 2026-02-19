<?php

namespace App\Models\Utility\Address;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    use HasFactory;

    protected $fillable = ["country_id", "province_id", "state_id",
        "city_id", "fax", "phone", "website", "whatsapp", "instagram",
        "address", "postal_code", "city_name", "mobile_country_id", "mobile", "address_id_in_ic_system"];

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function mobile_country()
    {
        return $this->belongsTo(Country::class, "mobile_country_id");
    }

    public static function GetAddressFromString($country_id, $province_id, $address, $postal_code, $city_name, $phone)
    {
        $address = Address::firstOrCreate(
            [
                "country_id" => $country_id,
                "province_id" => $province_id,
                "address" => $address,
                "postal_code" => $postal_code,
                "city_name" => $city_name,
                "phone" => $phone,
            ]
            ,
            [
                "country_id" => $country_id,
                "province_id" => $province_id,
                "address" => $address,
                "postal_code" => $postal_code,
                "city_name" => $city_name,
                "phone" => $phone,
            ]
        );

        return $address;
    }

}
