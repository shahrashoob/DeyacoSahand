<?php

namespace App\Models\Production;

use App\Models\Utility\Event;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use  App\Models\Worker;
use App\Models\Utility\Status;
use App\Models\Utility\Message;
use Carbon\Carbon;
use Illuminate\Support\Str;

class ProductionMethod extends Model
{
    use HasFactory;

    protected $table = "production_methods";
    protected $fillable = ["id", "description"];


    public function getCode()
    {
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


        return "PM ".$code;
    }
}