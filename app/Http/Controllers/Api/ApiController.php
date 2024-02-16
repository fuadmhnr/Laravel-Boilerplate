<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ApiController extends Controller
{
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

        $pagination = [
            'page' => $data->currentPage(),
            'first_page_url' => $data->url(1),
            'from' => $data->firstItem(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'items_per_page' => $itemsPerPage,
            'prev_page_url' => $data->previousPageUrl(),
            'to' => $data->lastItem(),
            'total' => $data->total(),
        ];

        // Construct the links array
        $links = [];

        // Previous link
        $links[] = [
            'url' => $pagination['prev_page_url'],
            'label' => '&laquo; Previous',
            'active' => false,
            'page' => null,
        ];

        // Page links
        for ($i = 1; $i <= $pagination['last_page']; $i++) {
            $links[] = [
                'url' => $data->url($i),
                'label' => $i,
                'active' => $i == $pagination['page'],
                'page' => $i,
            ];
        }

        // Next link
        $links[] = [
            'url' => $pagination['next_page_url'],
            'label' => 'Next &raquo;',
            'active' => false,
            'page' => null,
        ];

        $pagination['links'] = $links;

        $response = [
            'data' => $data,
            'payload' => [
                'pagination' => $pagination,
            ],
        ];

        return response()->json($response, 200);
    }
}
