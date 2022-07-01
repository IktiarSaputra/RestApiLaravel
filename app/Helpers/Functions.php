<?php

function sendResponse($result, $message){
    $response = [
        'success' => true,
        'message' => $message,
        'data' => $result,
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