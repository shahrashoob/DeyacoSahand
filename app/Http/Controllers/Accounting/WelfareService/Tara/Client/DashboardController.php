<?php

namespace App\Http\Controllers\Accounting\WelfareService\Tara\Client;

use App\Http\Controllers\Controller;
use App\Models\Accounting\WelfareService\WelfareService;
use App\Models\Utility\Setting;
use App\Models\Utility\Utility;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Milon\Barcode\DNS1D;

class DashboardController extends Controller
{
    //
    public $route_path = "accounting.welfare_service.tara.client.dashboard.";
    public $view_path = "accounting.welfare_service.tara.client.dashboard.";

    public function index()
    {
        $result_login = self::Login();

        if(!$result_login["result"]){
            return back()->withErrors($result_login["error"]);
        }
        $worker = Worker::find( Auth::id() );
        $result_balance= self::GetBalance($worker,$result_login["accessCode"]);
        if(!$result_balance["result"]){
            $balance="***";
        }
        $balance = $result_balance["balance"];
        return view($this->view_path . "index", compact("balance"));
    }

    public function purchase_key()
    {
        $result_login = self::Login();

        if(!$result_login["result"]){
            return back()->withErrors($result_login["error"]);
        }
        $worker = Worker::find( Auth::id() );

        $result_key=self::GetPurchase($worker, $result_login["accessCode"]);
        if(!$result_key["result"]){
            return back()->withErrors($result_login["error"]);
        }



        $barcode = DNS1D::getBarcodeSVG($result_key["barcode"], 'C39', 1.9, 50);
        $sconds = 60;
        return view($this->view_path . "purchase_key", ["barcode" => $barcode, "sconds" => $sconds]);
    }

    public function transactions()
    {
        $list = [];

        $welfare_service=WelfareService::where("user_id",Auth::id())->first();

        $result_login = self::Login();

        if(!$result_login["result"]){
            return back()->withErrors($result_login["error"]);
        }
        $worker = Worker::find( Auth::id() );

      return  $result_list=self::Transaction($welfare_service, $result_login["accessCode"]);

        return view($this->view_path . "transactions", compact("list"));
    }

    public static function GetPurchase(Worker $worker, $token)
    {

        $channelId = "42233";
       // env("Tara_ChannelId");
        $mobile ="0". $worker->mobile;

        $walft_service=WelfareService::where("user_id",$worker->id)->first();
        if(!$walft_service){
            return [
                "result" => false,
                "barcode" =>"اطلاعات ثبت نام شما در تارا نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید.",
            ];
        }
        $accountNumber = $walft_service->account_number;

        $RSASignature = Utility::GetRSASignature($accountNumber . "," . $mobile . "," . $channelId);

        $settings = [
            'base_uri' =>  "https://stage.tara-club.ir/club/api/wallet/v1/account/key/purchase/$channelId/$mobile/$accountNumber",
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer $token" ,
            ],
            'body' => [
                'sign' => $RSASignature['signature'],
                "accountNumber"=>$accountNumber,
                "channelId"=>$channelId,
                "mobile"=>$mobile,
            ]

        ];

        $client = new \GuzzleHttp\Client();
        $response = $client->post($settings["base_uri"], [
            'headers' => $settings["headers"],
            'json' => $settings["body"]
        ]);
        $data = $response = $response->getBody();
        $data = json_decode($data);
        if (!isset($data->success) || $data->success==true) {

            return [
                "result" => true,
                "barcode" => $data->barcode,
            ];

        } else {

            return [
                "result" => false,
                "error" => $data->data->code . " - " . $data->data->message,
            ];

        }

        /************************************/

//
//
//// Initialize a cURL session
//        $ch = curl_init();
//
//// Set the target URL
//        $url = "https://stage.tara-club.ir/club/api/wallet/v1/account/key/purchase/$channelId/$mobile/$accountNumber";
//        curl_setopt($ch, CURLOPT_URL, $url);
//
//// Set the request method to POST
//        curl_setopt($ch, CURLOPT_POST, true);
//
//// Set the HTTP headers
//        curl_setopt($ch, CURLOPT_HTTPHEADER, [
//            'Content-Type: application/json',
//            'Authorization: Bearer '.$token,
////            'Authorization: Bearer eyJhbGciOiJIUzUxMiJ9.eyJzdWIiOiIyNzIwOCIsImlhdCI6MTcyODc5OTU0OCwiZXhwIjoxNzQ0MzUxNTQ4LCJkZXZpY2UiOiJ0YWd0YXBfZmFsbGFoIiwicm9sZSI6IlJPTEVfUEFZLUlOSVQifQ.-tN35yLCjld15yTbUe65LsZYbv0m8k6uEW6Os2ngZhHpt-FFYZGLbai1QHib-JpNKDJSYq0AqaWYC9IrScfegQ',
//        ]);
//
//// Set the data to send in the POST request
//        $data = [
//            "mobile" => $mobile,
//            "channelId" => $channelId,
//            "accountNumber" =>$accountNumber,
//            "sign" => $RSASignature['signature'] //"eyJhbGciOiJIUzUxMiJ9.eyJdWIiOiIyNzIxNiIsImlhdCI6MTczNjY3NTMyMiwiZXhwIjoxNzUyMjI3MzIyLCJkZXZpY2UiOiJkaWFjb19zYW5kYm94X2NyZWRpdCIsInJvbGUiOiJST0xFX0NSRURJVCJ9.kpqmxyR8keA2gqYDdaDd4eACGq4wQnQNNqMe9CQJxwiWMx4vS-7fJ86Yfpc2HoMUPXuQJvLLEnHJ1mab8X_HKA"
//        ];
//        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data)); // Encode the data as JSON
//
//// Set options to return the response as a string
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//// Execute the request
//        $response = curl_exec($ch);
//
//// Check for errors
//        if (curl_errno($ch)) {
//            echo 'Error: ' . curl_error($ch);
//        } else {
//            // Decode and display the response
//            $decodedResponse = json_decode($response, true);
//            print_r($decodedResponse);
//        }
//
//// Close the cURL session
//        curl_close($ch);


    }

    public static function GetBalance(Worker $worker, $token)
    {
        $channelId = "42233";
        $mobile ="0". $worker->mobile;

        $walft_service=WelfareService::where("user_id",$worker->id)->first();
        if(!$walft_service){
            return [
                "result" => false,
                "barcode" =>"اطلاعات ثبت نام شما در تارا نامعتبر است، لطفا با واحد پشتیبانی تماس بگیرید.",
            ];
        }
        $accountNumber = $walft_service->account_number;

        $RSASignature = Utility::GetRSASignature($mobile . "," . $channelId . "," . $accountNumber);

        $settings = [
            'base_uri' => "https://stage.tara-club.ir/club/" . "api/wallet/limited/v1/account/balance/$channelId/$mobile",
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' => "Bearer " . $token,
            ],
            'body' => json_encode([
                'contractId' => $channelId,
                'mobile' => $mobile,
                'accountNumbers' => [$accountNumber],
                'sign' => $RSASignature['signature'],
            ]),

        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',

        );
         $data=$response = $request->getBody();
        $data = json_decode($data);
        if ($data->balanceData) {

            return [
                "result" => true,
                "balance" => $data->balanceData[0]->balance,
            ];

        } else {

            return [
                "result" => false,
                "error" =>"لطفا با واحد پشتیبانی تماس بگیرید.",
            ];

        }
    }

    public static function Login()
    {

        $settings = [
            'base_uri' => env("Tara_URL") . "club/api/v1/user/login/credit",
            'headers' => [
                'Content-Type' => 'application/json',
            ],
            'body' => json_encode([
                'principal' =>"init_saderat", /// env("Tara_Username"),
                'password' =>"init_saderat"// env("Tara_Password"),
            ])
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
        );
        $data = $response = $request->getBody();
        $data = json_decode($data);
        if ($data->success) {

            return [
                "result" => true,
                "accessCode" => $data->accessCode,
            ];

        } else {

            return [
                "result" => false,
                "error" => $data->data->code . " - " . $data->data->message,
            ];

        }
    }


    public static function Create(Worker $worker, $mobile, $token)
    {
        $contractId =852;// env("Tara_ContractId");

        $RSASignature = Utility::GetRSASignature($contractId . "," . $mobile . "," . $worker->national_code);

        $settings = [
            'base_uri' =>  "https://stage.tara-club.ir/club/api/v1/limited/account/create/" . $contractId,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' =>   "Bearer " . $token,
            ],
            'body' => [
                'mobile' => $mobile,
                'nationalCode' => $worker->national_code,
                'name' => $worker->firstname,
                'family' => $worker->lastname,
                'orgProfileInfo' => $worker->id, // کد سازمانی
                'gender' => $worker->gender_id,
                'birthdate' => jdate(Carbon::parse($worker->date_of_birth)->timestamp)->format('Y-m-d'),
                'sign' => $RSASignature["signature"],
            ]
        ];


        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($settings["base_uri"], [
                'headers' => $settings["headers"],
                'json' => $settings["body"]
            ]);
            $data = $response = $response->getBody();
            $data = json_decode($data, true);
            return $data;
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'Unable to create account',
                'message' => $e->getMessage(),
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : null,
            ], 500);
        }

    }
    public static function Charge(WelfareService $welfare_service, $amount,$traceNumber, $token,$type)
    {
        $contractId =852;// env("Tara_ContractId");

        $RSASignature = Utility::GetRSASignature($contractId . "," . $welfare_service->mobile . "," . $welfare_service->national_code.",".$amount);

        $settings = [
            'base_uri' =>  "https://stage.tara-club.ir/club/api/v2/limited/account/transaction/$type/" . $contractId,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' =>   "Bearer " . $token,
            ],
            'body' => [
                'mobile' => $welfare_service->mobile,
                'nationalCode' => $welfare_service->national_code,
                'amount' => $amount,
                'traceNumber' => $traceNumber,
                'sign' => $RSASignature["signature"],
            ]
        ];


        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($settings["base_uri"], [
                'headers' => $settings["headers"],
                'json' => $settings["body"]
            ]);
            $data = $response = $response->getBody();
            $data = json_decode($data);
            if ($data->success) {

                return [
                    "result" => true,
                    "referenceNumber" => $data->referenceNumber,
                ];

            } else {

                return [
                    "result" => false,
                    "error" => $data->data->code . " - " . $data->data->message,
                ];

            }
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'Unable to create account',
                'message' => $e->getMessage(),
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : null,
            ], 500);
        }

    }

    public static function DisCharge(WelfareService $welfare_service, $amount,$traceNumber, $token,$type)
    {
        $contractId =852;// env("Tara_ContractId");

        $RSASignature = Utility::GetRSASignature($contractId . "," . $welfare_service->mobile . "," . $welfare_service->national_code.",".$amount);

        $settings = [
            'base_uri' =>  "https://stage.tara-club.ir/club/api/v1/limited/account/transaction/$type/" . $contractId,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' =>   "Bearer " . $token,
            ],
            'body' => [
                'mobile' => $welfare_service->mobile,
                'nationalCode' => $welfare_service->national_code,
                'amount' => $amount,
                'traceNumber' => $traceNumber,
                'sign' => $RSASignature["signature"],
            ]
        ];


        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($settings["base_uri"], [
                'headers' => $settings["headers"],
                'json' => $settings["body"]
            ]);
            $data = $response = $response->getBody();
            $data = json_decode($data);
            if ($data->success) {

                return [
                    "result" => true,
                    "referenceNumber" => $data->referenceNumber,
                ];

            } else {

                return [
                    "result" => false,
                    "error" => $data->data->code . " - " . $data->data->message,
                ];

            }
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'Unable to create account',
                'message' => $e->getMessage(),
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : null,
            ], 500);
        }

    }

    public static function TraceCode(WelfareService $welfare_service, $amount,$type, $token)
    {
        $contractId =852;// env("Tara_ContractId");

        $RSASignature = Utility::GetRSASignature($contractId . "," . $welfare_service->mobile . "," . $welfare_service->national_code.",".$amount);

        $settings = [
            'base_uri' =>  "https://stage.tara-club.ir/club/api/v1/limited/account/transaction/trace/" . $contractId."/".$type,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' =>   "Bearer " . $token,
            ],
            'body' => [
                'mobile' => $welfare_service->mobile,
                'nationalCode' => $welfare_service->national_code,
                'amount' => $amount,
                'sign' => $RSASignature["signature"],
            ]
        ];


        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($settings["base_uri"], [
                'headers' => $settings["headers"],
                'json' => $settings["body"]
            ]);
            $data = $response = $response->getBody();
            $data = json_decode($data);
            if ($data->success) {

                return [
                    "result" => true,
                    "traceNumber" => $data->traceNumber,
                ];

            } else {

                return [
                    "result" => false,
                    "error" => $data->data->code . " - " . $data->data->message,
                ];

            }
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'Unable to create account',
                'message' => $e->getMessage(),
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : null,
            ], 500);
        }

    }


    public static function Transaction(WelfareService $welfare_service, $token)
    {
        $contractId =852;// env("Tara_ContractId");



        $settings = [
            'base_uri' =>  "https://stage.tara-club.ir/club/api/wallet/limited/v1/account/transactions/" . $contractId."/". $welfare_service->mobile ."/".$welfare_service->account_number,
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization' =>   "Bearer " . $token,
            ],
            'body' => [
                'pageNo' => 1,
                'pageSize' => 10,
                'accountNumber'=>$welfare_service->account_number,
            ]
        ];


        try {
            $client = new \GuzzleHttp\Client();
            $response = $client->post($settings["base_uri"], [
                'headers' => $settings["headers"],
                'json' => $settings["body"]
            ]);
            $data = $response = $response->getBody();
            $data = json_decode($data, true);
            return $data;
        } catch (RequestException $e) {
            return response()->json([
                'error' => 'Unable to create account',
                'message' => $e->getMessage(),
                'status' => $e->getResponse() ? $e->getResponse()->getStatusCode() : null,
            ], 500);
        }

    }
}
