<?php

namespace App\Http\Controllers;

use App\Services\UsersServices;
use Illuminate\Http\Request;

class UsersControllers extends Controller
{

    private UsersServices $usersServices;

    public function __construct(UsersServices $usersServices) {
        $this->usersServices = $usersServices;
    }

    public function get()
    {
        $this->usersServices->get();
    }
}
