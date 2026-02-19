<?php

namespace App\Http\Controllers\Accounting\Store;

use App\Http\Controllers\Controller;
use App\Models\Accounting\Store\Store;
use App\Models\Utility\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    public $route_path = "accounting.store.setting.";
    public $view_path = "accounting.store.setting.";

    public function index()
    {
        $list = Store::all();
        return view($this->view_path . "index", compact('list'));
    }

    public function store(Request $request)
    {
        $stores = Store::all();
        foreach ($stores as $store) {
            $store->price = $request->input('price_' . $store->id);
            $store->validity_date = $request->input('validity_date_' . $store->id);
            $store->save();
        }
        return redirect()->route($this->route_path . "index")->with(["success" => "تغییرات با موفقیت ثبت شد."]);
    }

}
