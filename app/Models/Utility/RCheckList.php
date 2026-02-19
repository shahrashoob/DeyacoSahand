<?php

namespace App\Models\Utility;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RCheckList extends Model
{
    use HasFactory;
    protected $table="r_check_list";

    public function get_datetime(){
        return jdate(Carbon::parse($this->created_at)->timestamp)->format('H:i:s Y/m/d ');
    }
}
