<?php

namespace App\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

trait ApiResponse
{
    protected function successResponse($data, $message = null, $code = Response::HTTP_OK): JsonResponse
    {
        if ($data instanceof \Illuminate\Http\Resources\Json\JsonResource) {
            return $data->additional([
                'message' => $message,
            ])->response()->setStatusCode($code);
        }

        return response()->json([
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message, $code): JsonResponse
    {
        return response()->json([
            'message' => $message,
        ], $code);
    }
}
