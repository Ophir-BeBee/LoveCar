<?php

if (!function_exists('sendResponse')) {
    function sendResponse($status,$message="No message",$data=null)
    {
        $response = [
            'data'    => $data,
            'message' => $message,
            'status' => $status
        ];

        return response()->json($response);
    }
}
