<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory;

    protected $fillable = ["text", "other_id", "message_type_id"];

    public static function convert_farsi_digits_to_english($string)
    {

        $fa_digits = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $en_digits = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];


        $string = str_replace($fa_digits, $en_digits, $string);

        $search = array('ك', 'ي', 'ة', 'ؤ', 'إ', 'أ', 'ٱ');
        $replace = array('ک', 'ی', 'ه', 'ؤ', 'ای', 'ا', 'ا');

        return str_replace($search, $replace, $string);

    }
}
