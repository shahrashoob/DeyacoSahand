<?php

namespace App\Http\Controllers\Utility;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Utility\Menu;

class MenuSettingController extends Controller
{
    //
    public function index(){

        $menus=Menu::get();

        return view("utility.menu.index",compact("menus"));
    }
}
