<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\PaginationService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    protected $paginationService;

    public function __construct(PaginationService $paginationService)
    {
        $this->paginationService = $paginationService;
    }

    public function paginateResponse(Model $model, $resourceClass, Request $request, $additionalConditions = null)
    {
        $page = $request->input('page', 1);
        $itemsPerPage = $request->input('items_per_page', 10);
        $sort = $request->input('sort', 'id');
        $order = $request->input('order', 'asc');
        $search = $request->input('search', '');

        $query = $model::orderBy($sort, $order);

        // Apply additional conditions based on your requirements
        if ($additionalConditions && is_callable($additionalConditions)) {
            $additionalConditions($query, $search);
        }

        $data = $resourceClass::collection(
            $query->paginate($itemsPerPage, ['*'], 'page', $page)
        );

        $pagination = $this->paginationService->generatePagination($data);

        $response = [
            'data' => $data,
            'payload' => [
                'pagination' => $pagination,
            ],
        ];

        return response()->json($response, 200);
    }
}
