<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Auth;

class ApiController extends Controller
{
    protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }
}
