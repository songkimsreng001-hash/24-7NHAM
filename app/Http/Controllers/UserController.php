<?php

namespace App\Http\Controllers;

class UserController extends Controller
{
    public function show($id)
    {
        return 'User ID is'.$id.' (from UserController)';
    }
}
