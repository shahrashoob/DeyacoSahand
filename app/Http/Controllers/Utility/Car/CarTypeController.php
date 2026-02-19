<?php

namespace App\Http\Controllers\Utility\Car;

use App\Http\Controllers\Controller;
use App\Models\LineProduct\Product;
use App\Models\Utility\Car\CarType;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class CarTypeController extends Controller
{
    public $route_path = "utility.car.car_type.";
    public $view_path = "utility.car.car_type.";

    public function index()
    {
        $list = CarType::paginate();
        return view($this->view_path . "index", compact("list"));
    }
    public function edit(CarType $car_type)
    {
        return view($this->view_path . "edit", compact("car_type"));
    }
    public function update(Request $request, CarType $car_type)
    {
        $car_type->update($request->all());
        return redirect()->route("utility.car.car_type.index")->with(["success" => "اطلاعات با موفقیت اضافه گردید."]);
    }
}
