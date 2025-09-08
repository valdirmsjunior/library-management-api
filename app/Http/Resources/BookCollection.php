<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class BookCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
    public function paginationInformation($request, $paginated, $default): array
    {
        return $default;
        // return [
        //     'pagination' => [
        //         'total' => $default['meta']['total'],
        //         'count' => isset($default['data']) ? count($default['data']) : 0,
        //         'per_page' => $default['meta']['per_page'],
        //         'current_page' => $default['meta']['current_page'],
        //         'last_page' => $default['meta']['last_page'],
        //         'total_pages' => $default['meta']['last_page'],
        //         'next_page_url' => $default['links']['next'] ?? null,
        //         'previous_page_url' => $default['links']['prev'] ?? null,
        //     ],
        //     'meta' => [
        //         'request_time' => now(),
        //         'response_time' => now(),
        //     ],
        //     'links' => [
        //         'self' => url()->current(),
        //         'first' => $default['links']['first'] ?? null,
        //         'last' => $default['links']['last'] ?? null,
        //         'next' => $default['links']['next'] ?? null,
        //         'prev' => $default['links']['prev'] ?? null,
        //     ],
        // ];
    }
}
