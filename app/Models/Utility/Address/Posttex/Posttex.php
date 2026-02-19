<?php

namespace App\Models\Utility\Address\Posttex;

use GuzzleHttp\Client;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Posttex extends Model {
    use HasFactory;

    public static function client() {
        return $client = new Client( [
            // Base URI is used with relative requests
            'base_uri' => 'https://postex.ir/api/',
            // You can set any number of default request options.
            'timeout'  => 2.0,
        ] );
    }

    public static function getToken() {
        $client   = Posttex::client();
        $response = $client->request( 'post', 'login',
            [
                'query' => [ "Username" => "09390418663", "Password" => "8812583" ]
            ] );
        $body     = $response->getBody();

        return json_decode( $body )->Token;
    }
}
