<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class TestBroadcastAuth extends Command
{
    protected $signature = 'test:broadcast-auth {user_id} {channel} {socket_id}';
    protected $description = 'Test /broadcasting/auth for a user';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $channel = $this->argument('channel');
        $socketId = $this->argument('socket_id');

        $user = User::find($userId);

        if (!$user) {
            $this->error("User not found");
            return 1;
        }

        // ایجاد API token موقت (Sanctum)
        $token = $user->createToken('broadcast-test')->plainTextToken;

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post(url('/dashboard'), [
            'channel_name' => $channel,
            'socket_id' => $socketId,
        ]);

        $this->info("Status: " . $response->status());
        $this->info("Response: " . $response->body());

        // حذف توکن بعد از تست (اختیاری)
        $user->tokens()->where('name', 'broadcast-test')->delete();

        return 0;
    }
}
