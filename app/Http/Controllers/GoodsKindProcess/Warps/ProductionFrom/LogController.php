<?php

namespace App\Http\Controllers\GoodsKindProcess\Warps\ProductionFrom;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public static $info = [
        "route"         => "warps.production_form.log.",
        "enable_status" => [  ],
        "button"        => [ "caption" => "مشاهده سابقه  ", "class" => "btn-primary" ],
        "view_path"     => "goods_kind_process.warps.production_form.implementation_period_form.",
        "message"       => [ "confirm" => "آیا از پایان طراحی اطمینان دارید؟" ],
    ];
}
