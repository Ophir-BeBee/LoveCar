<?php

if (!function_exists('sendResponse')) {
    function sendResponse($data,$status,$message="No message")
    {
        $response = [
            'data'    => $data,
            'message' => $message,
            'status' => $status
        ];

        return response()->json($response);
    }
}
