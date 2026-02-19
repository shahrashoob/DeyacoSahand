<?php

namespace App\Models\Utility;

use Carbon\Carbon;
use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Setting extends Model
{
    use HasFactory;
    use Loggable;

    protected $fillable = ["string_value", "integer_value", "double_value", "status_id"];

    public static function getValues()
    {
        $setting = Setting::get();
        $values = [];
        foreach ($setting as $item) {
            $values[$item->key] = $item;
        }

        return $values;
    }

    public static function getIntegerValue($key, $connection = false)
    {
        if ($connection) {
            return Setting::on($connection)->where("key", $key)->first()->integer_value;
        }

        return Setting::where("key", $key)->first()->integer_value;
    }
    public static function getIntegerValueList($key)
    {

        return Setting::whereIn("key", $key)->pluck("integer_value","key")->toArray();
    }

    public static function getDoubleValue($key, $connection = false)
    {
        if ($connection) {
            return Setting::on($connection)->where("key", $key)->first()->double_value;
        }

        return Setting::where("key", $key)->first()->double_value;
    }

    public static function getStringValue($key, $connection = false)
    {
        if ($connection) {
            return Setting::on($connection)->where("key", $key)->first()->string_value;
        }
        return Setting::where("key", $key)->first()->string_value;
    }

    public static function FinancialYear(): array
    {
        $year = Carbon::parse(Carbon::now())->format('Y');
        $start_date_time = Carbon::parse($year . "/" . Setting::getStringValue("financial_year_start"));
        $last_date_time = Carbon::parse($year . "/" . Setting::getStringValue("financial_year_end"));

        if ($start_date_time->greaterThan(Carbon::now())) {
            $start_date_time->addYear(-2);
        }
        if ($start_date_time->greaterThan($last_date_time) || $start_date_time->equalTo($last_date_time)) {
            $last_date_time->addYear();
        }
        $last_date_time->addDay();

        return [
            "start_date_time" => $start_date_time,
            "last_date_time" => $last_date_time
        ];
    }

    public static function UpdateApi($key)
    {
        $new_api = Str::random(50);
        $setting = Setting::where("key", $key)->first();
       $new_api= str_replace(array('0', '1', '2',"3","4","5","6","7","8","9"), 'A', $new_api);
        $setting->string_value = $new_api;
        $setting->save();
    }
}
