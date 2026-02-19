<?php

namespace App\Http\Controllers\HR\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;


class FaceController extends Controller
{
    public function uploadFace(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpg,jpeg,png|max:5120'
        ]);

        $image = $request->file('image');

        $folder = 'live_frames';
        Storage::disk('public')->makeDirectory($folder);

        $filename = now()->timestamp . "_" . $image->getClientOriginalName();
        $image->storeAs($folder,$filename,'public');
 $ip = $request->ip();
        try {
            $response = Http::attach(
                'image',
                fopen($image->getPathname(), 'r'),
                $filename
            )->post($ip.':5000/upload_face');

            if ($response->failed()) {
                return response()->json([
                    'result' => false,
                    'message' => 'Python server error'
                ], 500);
            }

            return response()->json([
                'result'  => (bool) $response->json('result'),
                'user_id' => $response->json('user_id'),
                'message' => $response->json('message')
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'result' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
