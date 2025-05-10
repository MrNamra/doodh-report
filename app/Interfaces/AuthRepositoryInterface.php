<?php

namespace App\Interfaces;

interface AuthRepositoryInterface
{
    public function login($req);
    public function register($request);
    public function updateProfile($request);
}
