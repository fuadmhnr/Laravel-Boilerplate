<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends ApiController
{
    public function index(Request $request)
    {
        return $this->paginateResponse(new Product(), ProductResource::class, $request, function ($query, $search) {
            $query->where('name', 'like', '%' . $search . '%');
        });
    }

    public function create(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|min:3',
                'quantity' => 'required|numeric'
            ]);

            if($validator->fails()) {
                $errors = $validator->errors()->all()[0];
                return response()->json([
                    'status' => false,
                    'message' => $errors,
                    'data' => []
                ], 422);
            }

            $product = Product::create([
                'name' => $request->name,
                'quantity' => $request->quantity
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Products successfully created',
                'data' => $product->toArray()
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}
