<?php

namespace App\Models\Utility\Address\Posttex;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    public static function get_all($stateId){
        return City::getListLocal();
        $client=Posttex::client();
        $token=Posttex::getToken();
        $response = $client->request( 'get', 'town/getTownsByStateId',
            [
                'headers' => [ "token" =>$token, ],
                'query'   => [ "stateId",$stateId ]
            ] );
        return  $body     =json_decode( $response->getBody());
    }

    public static function getListLocal(){
        return json_decode('[
    {
        "townName": "منطقه جنوب ( 18 پستی )",
        "townId": 585,
        "stateId": 1
    },
    {
        "townName": "منطقه جنوب شرق ( 11 پستی )",
        "townId": 4,
        "stateId": 1
    },
    {
        "townName": "منطقه جنوب شرق ( 17 پستی )",
        "townId": 579,
        "stateId": 1
    },
    {
        "townName": "منطقه جنوب غرب ( 13 پستی )",
        "townId": 580,
        "stateId": 1
    },
    {
        "townName": "منطقه شمال ( 15 پستی )",
        "townId": 582,
        "stateId": 1
    },
    {
        "townName": "منطقه شمال ( 19 پستی )",
        "townId": 583,
        "stateId": 1
    },
    {
        "townName": "منطقه شمال شرق ( 16 پستی )",
        "townId": 584,
        "stateId": 1
    },
    {
        "townName": "منطقه شمال غرب ( 14 پستی )",
        "townId": 581,
        "stateId": 1
    },
    {
        "townName": "شهریار",
        "townId": 9,
        "stateId": 1
    },
    {
        "townName": "اسلامشهر",
        "townId": 40,
        "stateId": 1
    },
    {
        "townName": "بهارستان",
        "townId": 111,
        "stateId": 1
    },
    {
        "townName": "ملارد",
        "townId": 112,
        "stateId": 1
    },
    {
        "townName": "پاکدشت",
        "townId": 113,
        "stateId": 1
    },
    {
        "townName": "ری",
        "townId": 114,
        "stateId": 1
    },
    {
        "townName": "قدس",
        "townId": 115,
        "stateId": 1
    }]');
    }
}
