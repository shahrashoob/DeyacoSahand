<?php

namespace App\Models\LineProduct\Carrier;

use App\Models\Utility\Unit;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarrierType extends Model
{
    use HasFactory;

    protected $fillable = [
        "caption",
        "placed_in_warehouse",
        "min_band_number",
        "max_band_number",
        "has_number_ability",
        "unit_id",
        "min_band_capacity",
        "max_band_capacity",
        "carrier_group_id",
        "system_can_define_new_carrier",
        "average_weight",
        "it_changes_volume_after_filling",
        "average_weight",
        "length",
        "width",
        "height",
        "product_code",
        'owner_personal_id_in_ic',
        'definer_personal_id_in_ic'
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function carrier_group()
    {
        return $this->belongsTo(CarrierGroup::class);
    }

    public static function Exists($caption, $id = false)
    {
        if ($id) {
            return CarrierType::where([
                "caption" => $caption,
            ])->where("id", "!=", $id)->exists();
        }

        return CarrierType::where(["caption" => $caption])->exists();
    }

    public function carriers()
    {
        return $this->hasMany(Carrier::class);
    }

    public static function getWeight(CarrierType $carrier_type, $carrier = null, $amount = 0, $unit = null)
    {
        $weight = 0;
        if ($carrier_type->has_number_ability) { // قابل شماره گذاری است.
            if ($carrier) {
                if ($carrier->weight <= 0) {
                    return [
                        "result" => false,
                        "error" => "وزن حامل  " . $carrier_type->caption . "(" . $carrier->code . ")" . " در سامانه ثبت نشده است."
                    ];
                }
                $weight += $carrier->weight;
            } else {
                return [
                    "result" => false,
                    "error" => "از آنجایی که حامل های  " . $carrier_type->caption . " قابلیت شماره گذاری دارد، لطفا کد حامل را وارد نمایید."
                ];
            }
        } else {
            if ($carrier_type->average_weight <= 0) {
                return [
                    "result" => false,
                    "error" => "وزن میانگین  " . $carrier_type->caption . " در سامانه ثبت نشده است."
                ];
            }
            $weight += $carrier_type->average_weight;
        }


        return [
            "result" => true,
            "weight" => $weight
        ];

    }

    public static function checkAmount(CarrierType $carrier_type, $amount, $number_of_sub_packing = 0)
    {
        $max = $number_of_sub_packing == 0 ?
            $carrier_type->max_band_capacity :
            $carrier_type->max_band_capacity * $carrier_type->max_band_number * $number_of_sub_packing;
//
        if ($amount > $max) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه ظرفیت " . $carrier_type->caption .
                    " حداکثر " .
                    ($carrier_type->max_band_capacity * $carrier_type->max_band_number) . " " .
                    $carrier_type->unit->caption
                    . " می باشد، امکان قرار دادن " . $amount . " " . $carrier_type->unit->caption .
                    "   کالا بر روی حامل امکان پذیر نمی باشد."

            ];;
        }

        if ($amount < $carrier_type->min_band_capacity * $carrier_type->min_band_number) {
            return [
                "result" => false,
                "error" => "با توجه به اینکه ظرفیت " . $carrier_type->caption .
                    " حداقل " .
                    ($carrier_type->min_band_capacity * $carrier_type->min_band_number) . " " .
                    $carrier_type->unit->caption
                    . " می باشد، امکان قرار دادن " . $amount . " " . $carrier_type->unit->caption . "   کالا بر روی حامل امکان پذیر نمی باشد."
            ];


        }

        return [
            "result" => true
        ];
    }

    public static function ShowCerrierType($carrier_type_id, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL')."/api/",
            'headers' => [
            ],
            'query' => [
                'token' => $token,
                'carrier_type_id' => $carrier_type_id,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "show_carrier",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function SearchCerrierType($search, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL')."/api/",
            'headers' => [
            ],
            'query' => [
                'token' => $token,
                'search' => $search
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "search_carrier",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function CreateCarrierTypeInIc($json_decode_list,$app_name, $token)
    {

        $settings = [
            'base_uri' => env('IC_URL')."/api/",
            'headers' => [
            ],
            'query' => [
                'list' => $json_decode_list,
                'token' => $token,
                'app_name'=>$app_name
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "create_carrier"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }
    public static function ExistCarrierTypeInIC($json_decode_list, $token)
    {

        $settings = [
            'base_uri' => env('IC_URL')."/api/",
            'headers' => [
            ],
            'query' => [
                'list' => $json_decode_list,
                'token' => $token,
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "exist_carrier_type"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }
}
