<?php

namespace App\Http\Controllers\HR\Chat;

use App\Http\Controllers\Controller;
use App\Models\Accounting\CostCenter;
use App\Models\LineProduct\Machine\MachineType;
use App\Models\HR\Committee\Committee;
use App\Models\HR\Committee\CommitteePost;
use App\Models\Post\PostChatSetting;
use App\Models\Utility\Option;
use App\Models\Utility\SpecialLicense\SpecialLicenseTypeExpert;
use App\Models\Worker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class ChatifyController extends Controller
{
    //
    private $route_path = "hr.chat.chat.";

    public function index()
    {
        PostChatSetting::UpdateChatSetting(Auth::id());
        foreach (Worker::where("name","")->get() as $user) {
            $user->name = $user->fullname();
            $user->save();
        }
        return redirect()->route("chat", 0);
    }
}