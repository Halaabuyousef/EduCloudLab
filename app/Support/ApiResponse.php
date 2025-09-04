<?php

namespace App\Support;


trait ApiResponse {
    protected function ok($data = null, string $message = 'OK', int $code = 200) {
        return response()->json(['success' => true, 'message' => $message, 'data' => $data], $code);
    }
    protected function fail($message = 'Error', $errors = null, int $code = 422) {
        return response()->json(['success' => false, 'message' => $message, 'errors' => $errors], $code);
    }
    protected function unauthorized($message = 'Unauthorized') {
        return $this->fail($message, null, 401);
    }
}