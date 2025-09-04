<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PrivacyController extends Controller
{
    public function privacy(Request $request)
    {
        return response()->json([
            'success' => true,
            'message' => 'OK',
            'data'    => [
                'title'     => 'Privacy Policy',
                'slug'      => 'privacy-policy',
                'body_html' => '
                    <h2>Privacy Policy</h2>
                    <p>We respect your privacy. This policy explains what data we collect, why we collect it, and how we protect it.</p>
                    <ul>
                      <li>Data We Collect: account info (name, email), logs for security.</li>
                      <li>Use of Data: improve service, reservations management.</li>
                      <li>Sharing: never sold; shared only when legally required.</li>
                    </ul>
                ',
            ],
        ]);
    }
}
