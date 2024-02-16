<?php

namespace App\Repositories;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

interface ProductRepository
{
    public function getAllProducts(Request $request);
    public function createProduct(array $data): JsonResponse;
}