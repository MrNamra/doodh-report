<?php

namespace App\Interfaces;

interface TransactionRepositoryInterface
{
    public function index($id);
    public function addTrangaction($request);
    public function updateTrangaction($request);
    public function list($request);
    public function removeTrangaction($request);
}
