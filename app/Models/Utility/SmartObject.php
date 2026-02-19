<?php

namespace App\Models\Utility;

use App\Models\Post\Post;
use App\Models\Post\PostSmartObject;
use App\Models\Post\PostUser;
use App\Models\Worker;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use mysql_xdevapi\Exception;

class SmartObject extends Model
{
    use HasFactory;

    protected $fillable = [
        "caption",
        "smart_object_type_id",
        "ip",
        "port",
        "contour",
        "contour1",
        "contour2",
        "contour3",
        "contour4",
        "status_id"
    ];

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function smart_object_type()
    {
        return $this->belongsTo(SmartObjectType::class);
    }
    public function worker()
    {
        return $this->belongsTo(Worker::class, "user_id");
    }

    public function updateToken()
    {

        $this->token = \Illuminate\Support\Str::random(10);
        $this->save();

        return $this->token;
    }

    public function get_server_ip(){
        $ip = Setting::getStringValue("smart_object_server_ip");
        return $ip;
    }
    public function get_server_port(){
        $port = Setting::getStringValue("smart_object_server_port");
        return $port;
    }

    public function getLastUpdate()
    {
        return jdate(Carbon::parse($this->updated_at)->timestamp)->format('H:i:s Y/m/d ');
    }

    public static function ExistsCode($caption, $id = false)
    {
        if ($id) {
            return SmartObject::where("caption", $caption)->where("id", "!=", $id)->exists();
        }

        return SmartObject::where("caption", $caption)->exists();
    }
//
//    public static function CheckPort($port, $id = false)
//    {
//        if ($port == 0) {
//            return true;
//        }
//        if ($id) {
//            return SmartObject::where("port", $port)->where("id", "!=", $id)->exists();
//        }
//
//        return SmartObject::where("port", $port)->exists();
//    }

    public static function getContour(SmartObject $smart_object)
    {

        try {

            $ip = Setting::getStringValue("smart_object_server_ip");
            $port = Setting::getStringValue("smart_object_server_port");
            $settings = [
                'base_uri' => "$ip:$port/",
                'headers' => [
                    'Accept' => 'application/json',
                ],
                'query' => [
                    'smart_id' => $smart_object->id,
                    'host' => $smart_object->ip,
                    'port' => $smart_object->port,
                ]
            ];
            $client = new \GuzzleHttp\Client($settings);
            $request = $client->request(
                'POST',
                "get_smart_object_weight"
            );
            $response = $request->getBody();

            $contour = json_decode($response, 1);
        } catch (Exception $exception) {
            return ["result" => false,"error"=>" به دلیل قطع بودن شبکه، امکان اتصال به اشیاء هوشمند برقرار نمی باشد. "];
        }

        if (isset($contour["error"])) {
            return ["result" => false, "error" => $contour["error"]];
        }
        $contour= str_replace ( "b'ST,GS,", "", $contour);
        $contour= str_replace ( ",kg", "", $contour);
        $contour= str_replace ( "\\r\\n", "", $contour);

        return ["result" => true, "data" => $contour];
    }

    /**
     * انتخاب مقدار باسکول و باسکول
     * @param $back_url
     * @return array|\Illuminate\Http\RedirectResponse|void
     */
    public static function GetScaleValue(){

        $post_ids = PostUser::getCurrentPostByShiftWorkAndLeaveOvertime("post_ids");
        $smart_object_list = PostSmartObject::whereIn("post_id", $post_ids)->get();
        $smart_object_value = null;
        $smart_object = null;
        $url_scale = null;
//        return [
//            "result"=>false,
//            "error"=>"اتصال به باسکول  برای پست سازمانی شما امکان پذیر نمی باشد."
//        ];
//        return [
//            "result"=>true,
//            "smart_object_value"=>30,
//            "smart_object" =>SmartObject::find(2),
//        ];
        switch (count($smart_object_list)) {
            case 0:
                // بررسی می کنیم که می توان به صورت دستی اطلاعات را وارد کند یا خیر
                $allow_enter_gross_weight_by_worker_to_posts = Post::whereIn("id", $post_ids)->sum("allow_enter_gross_weight_by_worker_to_posts");
                if ($allow_enter_gross_weight_by_worker_to_posts == 0) {
                    return [
                        "result"=>false,
                        "error"=>"اتصال به باسکول  برای پست سازمانی شما امکان پذیر نمی باشد."
                    ];
                }
                break;
            case 1:
                $smart_object_result = SmartObject::getContour($smart_object_list[0]->smart_object);
                if (!$smart_object_result["result"]) {
                    return [
                        "result"=>false,
                        "error"=>$smart_object_result["error"]
                    ];
                }
                $smart_object = $smart_object_list[0]->smart_object;
                break;
            default:

                if (!session("default_smart_object_id")) {
                    return [
                        "result"=>false,
                        "warning"=>"انتخاب یک باسکول"
                    ];

                } else {
                    $smart_object = SmartObject::where([
                        "id" => session("default_smart_object_id"),
                        "smart_object_type_id" => 2
                    ])->first();
                    if (!$smart_object) {
                        return [
                            "result"=>false,
                            "error"=>"باسکول  مورد نظر یافت نشد،"
                        ];
                    }
                    $smart_object_result = SmartObject::getContour($smart_object);
                }
        }

        if (isset($smart_object_result)) {
            if (!$smart_object_result["result"]) {
                return [
                    "result"=>false,
                    "error"=>$smart_object_result["error"]
                ];
            }
            $smart_object_value = (float)$smart_object_result["data"]["weight"];
        }

        return [
            "result"=>true,
            "smart_object_value"=>$smart_object_value,
            "smart_object" =>$smart_object,
        ];

    }



}
