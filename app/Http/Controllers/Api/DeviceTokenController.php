<?php

namespace App\Http\Controllers\Api;

use App\Models\DeviceToken;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'token'       => 'required|string',
            'platform'    => 'nullable|in:android,ios,web',
            'device_name' => 'nullable|string|max:255',
        ]);

        DeviceToken::updateOrCreate(
            ['token' => $data['token']],
            [
                'user_id'      => $request->user()->id,
                'platform'     => $data['platform'] ?? null,
                'device_name'  => $data['device_name'] ?? null,
                'last_seen_at' => now(),
            ]
        );

        return response()->noContent(); // 204
    }

    public function destroy(Request $request, string $token)
    {
        DeviceToken::where('token', $token)
            ->where('user_id', $request->user()->id)
            ->delete();

        return response()->noContent();
    }
}
