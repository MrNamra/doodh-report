<?php

namespace App\Interfaces;

interface SharingRepositoryInterface
{
    public function getReport($uuid);
    public function getSharedLink($request);
    public function removeSharedLink($request);
}
