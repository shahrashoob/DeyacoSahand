<?php

namespace App\Models\LineProduct\Packing;

use App\Models\LineProduct\Carrier\Carrier;
use App\Models\LineProduct\Carrier\CarrierType;
use App\Models\LineProduct\Product;
use App\Models\Utility\Status;
use Database\Seeders\OldSeeder\UnitTypeSeeder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PackingType extends Model
{
    use HasFactory;

    protected $fillable = [
        "code",
        "caption",
        "number_of_layer",
        "active_status_id",
        "many_degrees_can_fit_into_one",
        "packing_type_label_printing_type_id",
        "label_caption",
        "weight",
        "max_amount_of_production_form_separately",
        "it_is_possible_extract_production_form_separately",
        "max_row_to_display_sub_packing_in_print",
        "length",
        "width",
        "height",
        "weight_error_percentage",
        "create_sub_packing_form_in_creation",
        "printer_unit_display_type_id",
        'discharge_type_id',
        "note",

        "packaging_forms_include_brand",
        "take_amount_from_parent_production_card",
        "normal_amount",
        "normal_amount_unit_type_id",
    ];

    public function layers()
    {
        return $this->hasMany(PackingTypeLayer::class);
    }

    public function first_packing_type()
    {

        return $this->belongsTo(PackingType::class, "first_packing_type_id");
    }

    public function normal_amount_unit_type()
    {

        return $this->belongsTo(UnitTypeSeeder::class, "normal_amount_unit_type_id");
    }

    public function packing_type_label_printing_type()
    {

        return $this->belongsTo(PackingTypeLabelPrintingType::class,);
    }

    public static function HasSubPackingType($product_ids)
    {
        $product_ids[] = -1;
        $list = PackingType::with("first_packing_type")->
        join("packing_type_product", "packing_type_id", "packing_types.id")->
        whereIn("product_id", $product_ids)->
        select("packing_type_id", "first_packing_type_id")->
        get();
        $packing_type_has_sub_packing = [];
        foreach ($list as $item) {
            $packing_type_has_sub_packing[$item->packing_type_id] = isset($item->first_packing_type) ? 1 : 0;
        }

        return $packing_type_has_sub_packing;
    }

    public function active_status()
    {
        return $this->belongsTo(Status::class, "active_status_id");
    }

    public function discharge_type()
    {
        return $this->belongsTo(DischargeType::class, "discharge_type_id");
    }

    public function getCode()
    {
        if ($this->code != "") {
            return $this->code;
        }
        $code = Str::of($this->id)->
        when($this->id < 1000, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($this->id < 100, function ($string) {
            return Str::of('0')->append($string);
        })->
        when($this->id < 10, function ($string) {
            return Str::of('0')->append($string);
        }); // 0000
        $this->code = $code;
        $this->save();

        return $code;
    }

    public function fullCaption($limit=null)
    {
        $text=$this->code . " - " . $this->caption;
        if($limit){
           return Str::limit($text, $limit, '...');
        }
        // dd($text);
        return $text;
    }

    public static function ExistsCode($caption, $id = false)
    {
        if ($id) {
            return PackingType::where("caption", $caption)->where("id", "!=", $id)->exists();
        }

        return PackingType::where("caption", $caption)->exists();
    }

    public function update_layer_code()
    {
        $code = 1;
        foreach ($this->layers()->orderBy("id")->get() as $item) {
            $item->layer_code = $code;
            $item->save();
            $code++;
        }
    }

    /**
     * @param \App\Models\LineProduct\Packing\PackingType $packing_type
     * @param                                             $carrier
     * محاسبه وزن بسته بندی
     *
     * @return array
     */
    public static function getWeight(PackingType $packing_type, $carrier = null,$carrier_code=null)
    {
        $weight = $packing_type->weight;
        $last_layer = $packing_type->layers()->orderByDesc("layer_code")->first();
        if (!$last_layer) {
            return ["result" => false, "error" => "لایه های بسته بندی برای نوع بسته بندی با کد ".$packing_type->id." یافت نشد."];
        }

        $carrier_type = $last_layer->carrier_type;
        if (in_array($last_layer->carrier_type_id, [9900, 0])) { //فاقد حامل
            return [
                "result" => true,
                "weight" => $weight,
                "carrier_type" => $carrier_type
            ];
        } elseif ($carrier_type) {

            if(!$carrier && $carrier_code){
                $carrier=Carrier::where("code",$carrier_code)->where("carrier_type_id",$carrier_type->id)->first();
               if(!$carrier) {
                   return ["result" => false, "error" => "حامل   از نوع " . $carrier_type->caption . "با شماره $carrier_code در سیستم وجود ندارد. "];
               }
            }
            $result_carrier = CarrierType::getWeight($carrier_type, $carrier);
            if (!$result_carrier["result"]) {
                return $result_carrier;
            } else {
                $weight += $result_carrier["weight"];
            }
        } else {
            return ["result" => false, "error" => "نوع حامل نادرست است، لطفا با پشتیبانی تماس بگیرد."];
        }


        return [
            "result" => true,
            "weight" => $weight,
            "carrier_type" => $carrier_type
        ];
    }


    /**
     * @param \App\Models\LineProduct\Product $product
     * @param \App\Models\LineProduct\Packing\PackingType $packing_type
     * @param                                             $gross_weight
     * @param                                             $sub_packing_form_number
     * محاسبه مقدار خالص کالا با توجه به بسته بندی اصلی و فرعی
     *
     * @return array
     */
    public static function getAmountFromWeight(
        Product     $product,
        PackingType $packing_type,
                    $gross_weight,
                    $sub_packing_form_number,
                    $carrier = null,
                    $amount = null,
                    $sub_amount = null,
                    $check_amount_for_weight = true,
                    $allow_use_algorithm = false
    )
    {

        $message = "";

        $gross_weight = floatval($gross_weight);

        if (!$product) {
            return [
                "result" => false,
                "error" => "کالا نامعتبر است."
            ];
        }
        if (!$packing_type) {
            return [
                "result" => false,
                "error" => "بسته بندی نامعتبر است."
            ];
        }

        $packing_weight_result = PackingType::getWeight($packing_type, $carrier);
        if (!$packing_weight_result["result"]) {
            $message = $packing_weight_result["error"];
        }

        if ($packing_type->first_packing_type) {
            $sub_packing_weight_result = PackingType::getWeight($packing_type->first_packing_type, $carrier);
            if (!$sub_packing_weight_result["result"]) {
                $message = $sub_packing_weight_result["error"];
            }

//            if ( $sub_packing_form_number == 0 ) {
//                $message = ( "با توجه به اینکه  " . $packing_type->caption . " دارای بسته بندی فرعی می باشد، باید برای بسته بندی های فرعی حداقل یک بسته بندی انتخاب شود." );
//            }
        }

        if (!$packing_type->first_packing_type && $sub_packing_form_number != 0) {
            $message = "با توجه به اینکه بسته بندی دارای بسته بندی فرعی نمی باشد، تعداد بسته بندی های فرعی باید صفر باشد.";
        }

        if ($sub_packing_form_number < 0) {
            $message = "تعداد بسته بندی فرعی نمی تواند منفی باشد.";
        }

        if ($message != "") {
            return [
                "result" => false,
                "error" => $message
            ];
        }

        $packing_type_weight =
            $packing_weight_result["weight"];

        if ($packing_type->first_packing_type) {
            $packing_type_weight += $sub_packing_weight_result["weight"] * $sub_packing_form_number;
        }

        if ($check_amount_for_weight) {
            $get_amount_from_weight_result = Product::getAmountFromWeight($product, $gross_weight, $packing_type_weight, $amount, $sub_amount,true, $allow_use_algorithm);

            if (!$get_amount_from_weight_result["result"]) {
                $message = $get_amount_from_weight_result["error"];
            }
            elseif($get_amount_from_weight_result["weight"]<=0){
                $message="با توجه به وزن بسته بندی و حامل، وزن ناخالص وارد شده نا معتبر است،<br/>
                    لطفا یکبار دیگر وزن ناخالص را به صورت دقیق وارد نمایید.";
            }
            else {
                $gross_weight = $get_amount_from_weight_result["gross_weight"];
                $final_amount = $get_amount_from_weight_result["final_amount"];
                $sub_amount = $get_amount_from_weight_result["sub_amount"];
                $weight = $get_amount_from_weight_result["weight"];

            }
        } else {
            $gross_weight = 0;
            $final_amount = 0;
            $sub_amount = 0;
            $weight = 0;
        }

        if ($message == "") {
            return [
                "result" => true,
                "weight" => $weight,
                "gross_weight" => $gross_weight,
                "final_amount" => $final_amount,
                "sub_amount" => $sub_amount,
                "packing_type_weight" => $packing_type_weight
            ];
        } else {
            return [
                "result" => false,
                "error" => $message
            ];
        }
    }


    public static function getHigherPackingType(PackingType $packing_type)
    {
// به ازای هر نوع بسته بندی، بسته بندی های لایه های بالاتر که آن بسته بندی در یکی از لایه های آن است را برمی گرداند
//مثلا اگر برای بسته بندی بوبین صدا زده شود، ممکن است کارتن یا پالت را برگرداند.
        $list = [];
        $higher_packing_type_list = PackingType::where("first_packing_type_id", $packing_type->id)->get();
        foreach ($higher_packing_type_list as $higher_packing_type) {
            $list[] = $higher_packing_type;

            $higher_packing_type_new_list = PackingType::getHigherPackingType($higher_packing_type);
            foreach ($higher_packing_type_new_list as $higher_packing_type_new) {
                $list[] = $higher_packing_type_new;
            }
        }

        return $list;
    }

    public static function SearchPackingType($search, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . "/api/",
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
            "search_packing_type",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function ShowPackingType($packing_type_id, $token)
    {
        $settings = [
            'base_uri' => env('IC_URL') . "/api/",
            'headers' => [
            ],
            'query' => [
                'token' => $token,
                'packing_type_id' => $packing_type_id,
            ]
        ];

        $client = new \GuzzleHttp\Client($settings);

        $request = $client->request(
            'POST',
            "show_packing_type",
        );


        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function ExistPackingTypeInIC($json_decode_list, $token)
    {

        $settings = [
            'base_uri' => env('IC_URL') . "/api/",
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
            "exist_packing_type"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function CreatePackingTypeInIc($json_decode_list, $app_name, $token)
    {

        $settings = [
            'base_uri' => env('IC_URL') . "/api/",
            'headers' => [
            ],
            'query' => [
                'list' => $json_decode_list,
                'token' => $token,
                'app_name' => $app_name
            ]
        ];
        $client = new \GuzzleHttp\Client($settings);
        $request = $client->request(
            'POST',
            "create_packing_type"
        );
        $response = $request->getBody();

        $result = json_decode($response, 1);
        return $result;
    }

    public static function GetVolume(PackingType $packing_type){


        return $packing_type->width * $packing_type->length * $packing_type->height / 1000000000;
    }
}
