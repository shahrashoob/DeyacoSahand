<?php

namespace App\Http\Controllers\HR\Api;


use App\Models\HR\User\UserEntryImage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class ImageController extends Controller
{
    /**
     * آپلود یک عکس برای کاربر
     */
    public function upload(Request $request)
    { 
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'image' => 'required|image|max:2048' // حداکثر 2MB
        ]);

        $user = User::find($request->user_id);

        // شمارش عکس‌های قبلی کاربر
        $count = $user->user_entry_images()->count() + 1;

        // گرفتن پسوند عکس
        $extension = $request->file('image')->getClientOriginalExtension();

        // نامگذاری بر اساس user_id و شماره عکس
        $name =  $user->id . '_' . $count . '.' . $extension;

        // مسیر اختصاصی برای هر کاربر
        $folder = 'images/' . $user->id;

        // ذخیره عکس داخل پوشه کاربر
        $path = $request->file('image')->storeAs($folder, $name, 'public');

        // ایجاد رکورد در جدول entry_users_images
        $image =UserEntryImage::create([
            'user_id' => $user->id,
            'name' => $name,
            'path' => $path,
            'trained' => false,       // ستون جدید
            'trained_at' => null,     // ستون جدید
        ]);

        // پاسخ JSON شامل شماره عکس و وضعیت آموزش
        return response()->json([
            'message' => "عکس شماره $count با موفقیت آپلود شد",
            'image_number' => $count,
            'image' => $image
        ]);
    }

    /**
     * بروزرسانی وضعیت آموزش عکس
     */
    public function markImageTrained($id)
    {
        $image = UserEntryImage::findOrFail($id);
        $image->trained = true;
        $image->trained_at = Carbon::now();
        $image->save();

        return response()->json([
            'message' => "عکس با موفقیت آموزش داده شد",
            'image' => $image
        ]);
    }


    public function trainDailyImages(Request $request)
    {
        $images = UserEntryImage::where('trained', false)->get();
        $ip = $request->ip();
        $pythonUrl = $ip.':5000/train'; // URL سرور پایتون
        $failedImages = []; // آرایه برای ذخیره عکس‌هایی که موفق نشدند

        foreach ($images as $image) {
            $filePath = storage_path('app/public/' . $image->path);

            // بررسی وجود فایل قبل از ارسال
            if (!file_exists($filePath)) {
                $failedImages[] = [
                    'id' => $image->id,
                    'name' => $image->name,
                    'message' => 'فایل عکس یافت نشد'
                ];
                //Log::error("File not found for image ID {$image->id}: $filePath");
                continue;
            }

            try {
                $response = Http::attach(
                    'image', file_get_contents($filePath), $image->name
                )->post($pythonUrl, [
                    'user_id' => $image->user_id
                ]);

                if ($response->successful()) {
                    $respJson = $response->json();

                    // بررسی success از سرور پایتون
                    if (isset($respJson['success']) && $respJson['success'] === true) {
                        $image->trained = true;
                        $image->trained_at = now();
                        $image->save();
                    } else {
                        $failedImages[] = [
                            'id' => $image->id,
                            'name' => $image->name,
                            'message' => $respJson['message'] ?? 'خطای نامشخص از سرور پایتون'
                        ];
                        //Log::error("Python training failed for image ID {$image->id}: " . json_encode($respJson));
                    }
                } else {
                    $failedImages[] = [
                        'id' => $image->id,
                        'name' => $image->name,
                        'message' => 'خطا در پاسخ سرور پایتون: ' . $response->body()
                    ];
                    //Log::error("HTTP error while sending image ID {$image->id} to Python: " . $response->body());
                }

            } catch (\Exception $e) {
                $failedImages[] = [
                    'id' => $image->id,
                    'name' => $image->name,
                    'message' => 'خطا در ارسال به پایتون: ' . $e->getMessage()
                ];
                //Log::error("Exception while sending image ID {$image->id} to Python: {$e->getMessage()}");
            }
        }

        $successCount = $images->count() - count($failedImages);

        return response()->json([
            "success" => count($failedImages) === 0,
            "message" => "تعداد عکس‌های آموزش داده شده: $successCount",
            "failed" => $failedImages
        ]);
    }
}
