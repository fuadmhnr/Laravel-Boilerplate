<?php

namespace App\Services;

use Illuminate\Pagination\LengthAwarePaginator;

class PaginationService
{
    public function generatePagination(LengthAwarePaginator $data)
    {
        $pagination = [
            'page' => $data->currentPage(),
            'first_page_url' => $data->url(1),
            'from' => $data->firstItem(),
            'last_page' => $data->lastPage(),
            'next_page_url' => $data->nextPageUrl(),
            'items_per_page' => $data->perPage(),
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
        
        return $pagination;
    }
}