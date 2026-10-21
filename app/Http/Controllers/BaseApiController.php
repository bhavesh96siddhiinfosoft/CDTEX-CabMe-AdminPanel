<?php
namespace App\Http\Controllers;
use Symfony\Component\HttpFoundation\Response;

/**
 * Only errorResponse() is in use, from the two API auth middlewares. The class is
 * never extended and never instantiated, so it is a static helper in practice.
 */
class BaseApiController extends Controller
{
    public static function errorResponse($data=[], $message = '', $errors = [], $statusCode = Response::HTTP_INTERNAL_SERVER_ERROR){
        $data['status']=$statusCode;
        $data['message']=$message;
        $data['error']=$message;
        return response([
            'response_time'=>microtime(true) - LARAVEL_START,
            'data'=>$data,
        ],$statusCode);
    }
}
