<?php

namespace App\Http\Controllers\Utility\SpecialLicense\Panel;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Packing\PackingType;
use App\Models\Utility\JsonDataList;
use App\Models\Utility\Option;
use App\Models\Utility\SpecialLicense\SpecialLicenseType;
use Illuminate\Http\Request;
use function PHPUnit\Framework\isEmpty;

class NewSpecialLicense13Controller extends Controller
{
    private $view_path = "utility.special_license.panel.new_special_license_13.";
    private $route_path = "utility.special_license.panel.new_special_license_13.";


    public function index()
    {
        $packing_type = session("packing_type");
        if (isset($packing_type) && !$packing_type) {
            return back()->withErrors("با خطایی مواجه شده اید لطفا دوباره امتحان کنید.");
        }
        $packing_type_options[] = ["id" => "0", "text" => "لطفا نوع بسته بندی را انتخاب کنید", "value" => ""];

        foreach (PackingType::get() as $item) {

            if ($item->layers()->orderByDesc("id")->first() &&
                $item->layers()->orderByDesc("id")->first()->carrier_type_id == $packing_type['carrier_type_id']
            ) {
                $option = ["value" => $item->id, "text" => $item->caption];

                $packing_type_options[] = $option;
            }
        }


        $carrier_option = Option::get("carrier_type", 0);

        return view($this->view_path . "index", compact("packing_type", "carrier_option", "packing_type_options"));
    }

    public function store(Request $request)
    {

        $packing_type = session("packing_type");
        $merged_data = array_merge($packing_type ?? [], $request->all());
        session()->put('packing_type', $merged_data);
        if (empty($merged_data)) {
            return back()->withErrors("با خطایی مواجه شده اید لطفا دوباره امتحان کنید.");
        }
        $NSLC = new NewSpecialLicenseController();

        $special_license_type = SpecialLicenseType::find(13);

        return $NSLC->store($request, $special_license_type, 0);

    }

}