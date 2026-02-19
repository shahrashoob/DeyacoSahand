<?php

namespace App\Http\Controllers\HR\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PythonController extends Controller
{
    public function getTrainingImages($userId)
    {
        $path = "images/$userId";

        if (!Storage::disk('public')->exists($path)) {
            return response()->json([
                "success" => false,
                "message" => "هیچ عکسی برای آموزش پیدا نکرد"
            ], 404);
        }

        $files = Storage::disk('public')->files($path);

        $images = array_map(function ($file) {
            return asset("storage/" . $file); // لینک عمومی عکس
        }, $files);

        return response()->json([
            "success" => true,
            "user_id" => $userId,
            "images" => array_map(fn($url) => ['file' => $url], $images),
            "message" => count($images) . " training images found"
        ]);
    }

    public function getAllUserIds()
    {
        $path = "images";
        if (!Storage::disk('public')->exists($path)) {
            return response()->json([
                "success" => false,
                "user_ids" => [],
                "message" => "No users found"
            ], 404);
        }

        $dirs = Storage::disk('public')->directories($path);

        // فقط اسم فولدرها (userId)
        $user_ids = array_map(fn($dir) => basename($dir), $dirs);

        return response()->json([
            "success" => true,
            "user_ids" => $user_ids
        ]);
    }
}
