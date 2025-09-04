<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AboutController extends Controller
{
    public function show()
    {
        $admins = Admin::orderBy('id')
            ->get()
            ->map(function (Admin $a) {
                $avatar = $a->avatar_url ?? $a->image ?? $a->avatar ?? null;
                if ($avatar && ! str_starts_with($avatar, 'http')) {
                    $avatar = Storage::url($avatar);
                }

                return [
                    'id'         => $a->id,
                    'name'       => $a->name,
                    'role'       => $a->job_title ?? $a->title ?? 'Admin',
                    'email'      => $a->email,
                    'avatar_url' => $avatar,
                ];
            });

        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data' => [
                'who_we_are' => 'We are a passionate team dedicated to providing modern solutions for education and technology. Our goal is to make learning accessible, interactive, and enjoyable for everyone.',
                'vision'     => 'To create a world where knowledge is available to all with just a click, empowering learners and educators globally.',
                'mission'    => 'Delivering innovative and easy-to-use digital tools that bridge the gap between technology and education.',
                'team_members' => $admins,
            ],
        ]);
    }
}
