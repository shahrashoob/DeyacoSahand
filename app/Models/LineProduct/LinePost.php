<?php

namespace App\Models\LineProduct;

use Haruncpi\LaravelUserActivity\Traits\Loggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LinePost extends Model {
    use HasFactory;
    use Loggable;
    protected $table = "line_post";
    protected $fillable = [ "post_id", "line_id", "station_id", "machine_type_id", "machine_id" ];

}
