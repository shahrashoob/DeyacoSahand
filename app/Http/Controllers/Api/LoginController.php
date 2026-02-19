<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Contractor\Contractor;
use App\Models\User;
use App\Models\Utility\Setting;
use App\Models\Utility\SmartObject;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    public function login(Request $request)
    {
        if (!$request->cooperation_type_id) {

            $request->cooperation_type_id = 5;//اشیا هوشنمد
        }

        switch ($request->cooperation_type_id) {

            case 5://اشیا هوشمند
                try {
                    $validateUser = Validator::make($request->all(),
                        [
                            'email' => 'required',
                            'password' => 'required'
                        ]);

                    if ($validateUser->fails()) {
                        return response()->json([
                            'result' => false,
                            'status' => 402,
                            'massage_type' => "error",
                            'code' => 51,
                            'message' => 'خطای اعتبارسنجی' . ": " . $validateUser->errors(),
                        ], 402);
                    }

                    if (!Auth::attempt($request->only(['email', 'password']))) {
                        return response()->json([
                            'result' => false,
                            'status' => 401,
                            'massage_type' => "error",
                            'code' => 52,
                            'message' => '.نام کاربری یا پسورد به درستی وارد نشده است',
                        ], 401);
                    }

                    $user = User::where('email', $request->email)->first();
                    $smart = SmartObject::where("user_id", $user->id)->first();
                    if (!$smart) {
                        return response()->json([
                            'result' => false,
                            'status' => 401,
                            'massage_type' => "error",
                            'code' => 53,
                            'message' => '.نام کاربری یا پسورد وارد شده مربوط به اشیاء هوشمند نمی باشد.',
                        ], 401);
                    }

                    return response()->json([
                        'result' => true,
                        'status' => 200,
                        'massage_type' => "success",
                        'message' => '.شما باموفقیت وارد شدید',
                        'token' => $user->createToken("API TOKEN")->plainTextToken
                    ], 200);

                } catch (\Throwable $th) {
                    return response()->json([
                        'result' => false,
                        'status' => 500,
                        'massage_type' => "error",
                        'code' => 54,
                        'message' => $th->getMessage()
                    ], 500);
                }
                break;
            case 2: // پیمانکار
            case 21:

                try {
                    $validateUser = Validator::make($request->all(),
                        [
                            'email' => 'required',
                            'password' => 'required'
                        ]);

                    if ($validateUser->fails()) {
                        return response()->json([
                            'result' => false,
                            'status' => 402,
                            'massage_type' => "error",
                            'code' => 21,
                            'message' => 'خطای اعتبارسنجی' . ": " . $validateUser->errors(),
                        ], 200);
                    }

                    if (!Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
                        return response()->json([
                            'result' => false,
                            'status' => 401,
                            'massage_type' => "error",
                            'code' => 22,
                            'message' => '.نام کاربری یا پسورد برای ورود به سامانه پیمانکار معتبر نمی باشد، لطفا با واحد فناوری اطلاعات تماس بگیرید.',
                        ], 200);
                    }
                    $user = User::where("email", $request->email)->first();
                    $api_key = Setting::getStringValue("api_key");

                    if ($api_key != $request->api_key) {
                        return response()->json([
                            'result' => false,
                            'status' => 200,
                            'massage_type' => "error",
                            'code' => 23,
                            'message' => 'API Key برای اتصال به سامانه پیمانکار نامعتبر است، لطفا با واحد فناوری اطلاعات تماس بگیرد.'
//                            $request->api_key."<br/>".$api_key,
                        ], 200);
                    }

                    return response()->json([
                        'result' => true,
                        'status' => 200,
                        'massage_type' => "success",
                        "code" => 24,
                        'message' => '.شما باموفقیت وارد شدید',
                        'token' => $user->createToken("API TOKEN")->plainTextToken
                    ], 200);

                } catch (\Throwable $th) {
                    return response()->json([
                        'result' => true,
                        'status' => 500,
                        'massage_type' => "error",
                        'message' => $th->getMessage()
                    ], 500);
                }

                break;
            case 1:// کارمند

                {
                    $validateUser = Validator::make($request->all(),
                        [
                            'email' => 'required',
                            'password' => 'required'
                        ]);

                    if ($validateUser->fails()) {
                        return response()->json([
                            'result' => false,
                            'status' => 402,
                            'massage_type' => "error",
                            'code' => 51,
                            'message' => 'خطای اعتبارسنجی' . ": " . $validateUser->errors(),
                        ], 402);
                    }

                    if (!Auth::attempt($request->only(['email', 'password']))) {
                        return response()->json([
                            'result' => false,
                            'status' => 401,
                            'massage_type' => "error",
                            'code' => 52,
                            'message' => '.نام کاربری یا پسورد به درستی وارد نشده است',
                        ], 401);
                    }

                    $user = User::where('email', $request->email)->first();
                    if (!Auth::attempt($request->only(['email', 'password']))) {
                        return response()->json([
                            'result' => false,
                            'status' => 401,
                            'massage_type' => "error",
                            'code' => 52,
                            'message' => '.نام کاربری یا پسورد به درستی وارد نشده است',
                        ], 401);
                    }


                    return response()->json([
                        'result' => true,
                        'status' => 200,
                        'massage_type' => "success",
                        'message' => '.شما باموفقیت وارد شدید',
                        'token' => $user->createToken("API TOKEN")->plainTextToken
                    ], 200);
                }
                try {

                } catch (\Throwable $th) {
                    return response()->json([
                        'result' => false,
                        'status' => 500,
                        'massage_type' => "error",
                        'code' => 54,
                        'message' => $th->getMessage()
                    ], 500);
                }


        }


    }


    public function logout(Request $request)
    {

        if ($request->user()->currentAccessToken()) {
            $request->user()->currentAccessToken()->delete();

            return response()->json([
                'result' => true,
                'massage_type' => "error",
                'message' => 'شما با موفقیت خارج شدید.'
            ]);
        } else {
            return response()->json([
                'result' => false,
                'massage_type' => "success",
                'message' => 'این توکن وجود ندارد'
            ]);
        }
    }
}
