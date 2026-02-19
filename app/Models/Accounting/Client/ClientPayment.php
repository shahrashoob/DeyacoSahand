<?php

namespace App\Models\Accounting\Client;

use App\Models\Order\Order;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        "user_id",
        "amount",
        "client_transaction_type_id",
        "description",
        "driver",
        "client_transaction_id",
        "status_id",
        "driver_status_id",
        'driver_order_id',
        'payment_id_in_deyaco',
        'random',
        'order_id_in_deyaco','track_id',
        "order_id"
    ];

    public static function CreatePaymentInDeyaco($amount, $Company_keyword)
    {

        $settings = [
            'base_uri' => "deyaco.ir/api/",
            'headers' => [
            ],

            'query' => [
                'amount' => $amount,
                'Company_keyword' => $Company_keyword,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "create_payment"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }
    public static function CheckConnectionInDeyaco()
    {
        try {
            $settings = [
                'base_uri' => "http://deyaco.ir/api/",
                'headers' => [],
                'query' => []
            ];
            $client = new \GuzzleHttp\Client($settings);

            $request = $client->request('POST', "check_connection");

            $response = $request->getBody();

            $result = json_decode($response, true);
            return $result;
        } catch (\Exception $e) {
            return [
                "result" => false,
                "error" => "اتصال شما به اینترنت برقرار نمی باشد."
            ];
        }
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
