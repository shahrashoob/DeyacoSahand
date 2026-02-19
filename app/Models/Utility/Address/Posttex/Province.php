<?php

namespace App\Models\Utility\Address\Posttex;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model {
    use HasFactory;

    public static function get_all() {//town/getTowns
        return Province::getListSample();
        $client   = Posttex::client();
        $token    = Posttex::getToken();
        $response = $client->request( 'get', 'state/getState',
            [
                'headers' => [ "token" => $token, ],
                'query'   => []
            ] );

        return $body = json_decode( $response->getBody() );
    }

    public static function getListSample() {
        return
            json_decode('[
    {
        "stateName": "تهران",
        "stateId": 1
    },
    {
        "stateName": "آذربایجان شرقی",
        "stateId": 2
    },
    {
        "stateName": "آذربایجان غربی",
        "stateId": 82
    },
    {
        "stateName": "اردبیل",
        "stateId": 245
    }]');
    }

}
