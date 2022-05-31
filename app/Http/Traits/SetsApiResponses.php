<?php

namespace App\Traits;

trait SetsApiResponses
{
    static function setResponse ($response)
    {
        $code = $response['status']['code'];
        isset($response['status']['message'])
            ? $message = $response['status']['message']
            : $message=self::getMessageFromCode($response['status']['code']);
        if (isset($response['data']) && isset($response['error'])){
            return response()->json([
                'status' => [
                    'code' => $code,
                    'message' => $message
                ],
                'data' => $response['data'],
                'error' => $response['error']
            ], $code);
        }
        elseif (isset($response['data'])){
            return response()->json([
                'status' => [
                    'code' => $code,
                    'message' => $message
                ],
                'data' => $response['data'],
                'error' => null
            ], $code);
        }
        elseif (isset($response['error'])){
            return response()->json([
                'status' => [
                    'code' => $code,
                    'message' => $message
                ],
                'data' => null,
                'error' => $response['error']
            ], $code);
        }
        else {
            return response()->json([
                'status' => [
                    'code' => $code,
                    'message' => $message
                ],
                'data' => null,
                'error' => null
            ], $code);
        }
    }

    static function getMessageFromCode ($statusCode)
    {
        switch ($statusCode) {
            case "200":
                return "Successful.";
                break;
            case "201":
                return "Successful and created related record.";
                break;
            case "400":
                return "Bad request.";
                break;
            case "401":
                return "Authorization has been denied for this request.";
                break;
            case "403":
                return "Forbidden.";
                break;
            case "404":
                return "No resource was found that matches your request.";
                break;
            case "406":
                return "Not Acceptable";
                break;
            default:
                return "Unknown error.";
        }
    }
}