<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Auth;

class ApiController extends Controller
{
	use ResponseTrait;
	
    protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function test()
    {
        return $this->respondHttp200([
            'data' => 'Prueba de API',
        ]);  
    }
}
