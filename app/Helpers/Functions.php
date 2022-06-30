<?php

function sendResponse($result, $message){
    $response = [
        'success' => true,
        'message' => $message,
        'token' => $result['token'],
    ];
    return response()->json($response, 200);
}

function sendError($error, $errorMesaage = [], $code = 404){
    $response = [
        'success' => false,
        'message' => $error,
    ];

    !empty($errorMesaage) ? $response['error'] = $errorMesaage : null ;

    return response()->json($response, $code);
}