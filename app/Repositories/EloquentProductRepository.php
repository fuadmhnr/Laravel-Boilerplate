<?php

namespace App\Repositories;

use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EloquentProductRepository implements ProductRepository
{
    public function getAllProducts(Request $request)
    {
        $page = $request->input('page', 1);
        $itemsPerPage = $request->input('items_per_page', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');
        $search = $request->input('search', '');

        $query = Product::orderBy($sort, $order);

        // Apply additional conditions based on your requirements
        if (!empty($search)) {
            $query->where('name', 'like', '%' . $search . '%');
        }

        return $query->paginate($itemsPerPage, ['*'], 'page', $page);
    }

    public function createProduct(array $data): JsonResponse
    {
        try {
            $product = Product::create([
                'name' => $data['name'],
                'quantity' => $data['quantity']
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Product successfully created',
                'data' => $product->toArray()
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }
}