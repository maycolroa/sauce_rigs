<?php

namespace App\Traits;

use InvalidArgumentException;

trait ResponseTrait
{

    /**
     * Responds a http request as a json
     * @param  Array   $data
     * @param  Integer $status
     * @param  Array   $headers
     * @param  Integer $options
     * @return \Illuminate\Http\Response
     */
    protected function respond($data = [], $status = 200, $headers = [], $options = 0)
    {
        return response()->json($data, $status, $headers, $options);
    }

    /**
     * responds a http request with some defined data
     * @param  Array   $response
     * @param  String  $header
     * @param  Integer $status
     * @return \Illuminate\Http\Response
     */
    protected function respondWithData($response = [], $header = 'message', $status = 200)
    {
        if (is_string($response)) {
            $data = [
                $header => $response,
                'status' => $status
            ];
        } elseif ($this->isAssoc($response)) {
            $data = $response;
        } else {
            throw new InvalidArgumentException('The sent data must be string or associative array');
        }
        return $this->respond($data, $status);
    }

    /**
     * Responds a http request with an error message
     * @param  String  $message
     * @param  Integer $status
     * @return \Illuminate\Http\Response
     */
    protected function respondWithError($response = '', $status = 500)
    {
        return $this->respondWithData($response, 'error', $status);
    }

    /**
     * Responds a http request with a message. Most Used for 2xx codes
     * @param  String  $response
     * @param  Integer $status
     * @return \Illuminate\Http\Response
     */
    protected function respondWithMessage($response = '', $status = 200)
    {
        return $this->respondWithData($response, 'message', $status);
    }

    /**
     * Responds a http request as 200 => OK
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp200($response = 'OK')
    {
        return $this->respondWithMessage($response, 200);
    }    

    /**
     * Responds a http request as 200 => OK
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondOK($response = 'OK')
    {
        return $this->respondHttp200($response);
    }

    /**
     * Responds a http request as 400 => Bad Request
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp400($response = 'Bad Request')
    {
        return $this->respondWithError($response, 400);
    }

    /**
     * Responds a http request as 400 => Bad Request
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondBadRequest($response = 'Bad Request')
    {
        return $this->respondHttp400($response);
    }

    /**
     * Responds a http request as 403 => Forbidden
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp403($response = 'Forbidden')
    {
        return $this->respondWithError($response, 403);
    }

    /**
     * Responds a http request as 403 => Forbidden
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondForbidden($response = 'Forbidden')
    {
        return $this->respondHttp403($response);
    }

    /**
     * Responds a http request as 404 => Not Found
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp404($response = 'Not Found')
    {
        return $this->respondWithError($response, 404);
    }

    /**
     * Responds a http request as 404 => Not Found
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondNotFound($response = 'Not Found')
    {
        return $this->respondHttp404($response);
    }

    /**
     * Responds a http request as 422 => Unprocessable Entity
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp422($response = 'Unprocessable Entity')
    {
        return $this->respondWithError($response, 422);
    }

    /**
     * Responds a http request as 422 => Unprocessable Entity
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondUnprocessableEntity($response = 'Unprocessable Entity')
    {
        return $this->respondHttp422($response);
    }

    /**
     * Responds a http request as 500 => Internal Error
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp500($response = 'Internal Error')
    {
        return $this->respondWithError($response, 500);
    }

    /**
     * Responds a http request as 500 => Internal Error
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondInternalError($response = 'Internal Error')
    {
        return $this->respondHttp500($response);
    }

    /**
     * Responds a http request as 503 => Service Unavailable
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp503($response = 'Service Unavailable')
    {
        return $this->respondWithError($response, 503);
    }

    /**
     * Responds a http request as 401 => Unauthorized
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondHttp401($response = 'Unauthorized')
    {
        return $this->respondWithError($response, 401);
    }

    /**
     * Responds a http request as 503 => Service Unavailable
     * @param  String $message
     * @return \Illuminate\Http\Response
     */
    protected function respondServiceUnavailable($response = 'Service Unavailable')
    {
        return $this->respondHttp503($response);
    }

    /**
     * checks if $arr is associative array
     * @param  Array|Collection   $arr
     * @return Boolean
     */
    private function isAssoc($arr)
    {
        if (array() === $arr) {
            return false;
        }
        return array_keys($arr) !== range(0, count($arr) - 1);
    }

}