<?php

namespace App\Helpers;

use Illuminate\Http\Resources\Json\JsonResource;

class Response extends JsonResource
{
    /**
     * Retrive data and convert to json.
     * 
     * @param mixed $data
     * @param string|null $message
     * @param int $code
     * @return \Illuminate\Http\JsonResponse
     */
    public static function json($data, $message, int $code)
    {
        if ($data instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            $response = $data->jsonserialize();
        } else {
            $response['data'] = $data;
        }
        
        $response['response']['success'] = Response::isStatusCodeSuccess($code);
        $response['response']['message'] = $message;
        return response()->json($response, $code);
    }

    public static function exception($exception, $message = null, $code = 400)
    {
        $response['response']['success'] = false;
        $response['response']['message'] = $message;
        $response['response']['message_error'] = $exception->getMessage();
        return response()->json($response, $code);
    }

    static function isStatusCodeSuccess($code): Bool
    {
        return $code < 399;
    }
}