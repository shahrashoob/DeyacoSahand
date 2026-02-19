<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class Report1008Controller extends Controller
{
    // داشبورد مرتبط با اسکریپت 1004
    public function index() {
        return view( "report/1008/index" );
    }
}
