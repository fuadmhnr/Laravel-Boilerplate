<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ProductResource;
use App\Repositories\EloquentProductRepository;
use App\Services\PaginationService;
use App\Services\ProductValidationService;
use Illuminate\Http\Request;

class ProductController extends ApiController
{
    protected $productRepository;
    protected $validationService;
    protected $paginationService;

    public function __construct(EloquentProductRepository $productRepository, ProductValidationService $validationService, PaginationService $paginationService)
    {
        $this->productRepository = $productRepository;
        $this->validationService = $validationService;
        $this->paginationService = $paginationService;
    }

    public function index(Request $request)
    {
        $data = $this->productRepository->getAllProducts($request);
        $resourceClass = ProductResource::class;
        $pagination = $this->paginationService->generatePagination($data);

        $response = [
            'data' => $resourceClass::collection($data),
            'payload' => [
                'pagination' => $pagination
            ],
        ];

        return response()->json($response, 200);
    }

    public function create(Request $request)
    {
        $data = $request->all();

        $validationResult = $this->validationService->validationCreateProduct($data);

        if (!$validationResult['status']) {
            return $this->jsonResponse($validationResult, 422);
        }

        return $this->productRepository->createProduct($data);
    }
}
