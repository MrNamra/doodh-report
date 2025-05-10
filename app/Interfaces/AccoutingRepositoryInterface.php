<?php

namespace App\Interfaces;

interface AccoutingRepositoryInterface
{
    public function addMoney($request);
    public function updateAccounting($request);
    public function addTrangcation($request);
    public function removeAccout($request);
}
