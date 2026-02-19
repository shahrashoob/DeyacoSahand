<?php

namespace App\Http\Controllers\HR\Company;

use App\Http\Controllers\Controller;
use App\Models\HR\Company\Company;
use App\Models\Utility\Option;
use Illuminate\Http\Request;

class dashboardController extends Controller
{
    private $view_path = "hr.company.dashboard.";
    private $route_path = "hr.company.dashboard.";


    public function index()
    {
        $list = Company::paginate(50);
        return view($this->view_path . "index", compact('list'));
    }

}
