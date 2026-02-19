<?php

namespace App\Models\Utility;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Priority extends Model
{
    use HasFactory;
    public static function GetIdFromText($caption){

        $id=3;
        switch($caption){
            case 1:
            case 2:
            case 9:
                $id=$caption;
            break;

        }
       return $id;

    }
    public function getHtml(){
        switch ($this->id){
            case 201:
                return $this->caption;
                break;
            case 202:
            case 203:
            return "<span style='color: #d79f10'>$this->caption</span>";
                break;
            case 204:
                return "<span style='color: red'>$this->caption</span>";
                break;
        }
    }

}
