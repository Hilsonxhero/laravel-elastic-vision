<?php

namespace Hilsonxhero\Xauth\Http\Controllers;

use Hilsonxhero\Xauth\Http\Controllers\BaseController;



class AuthController extends BaseController
{
    public function index()
    {
        return view('Xauth::home');
    }
}
