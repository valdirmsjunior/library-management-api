<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class MemberCollection extends ResourceCollection
{
    public function toArray($request): array
    {
        return parent::toArray($request);
    }
    public function paginationInformation($request, $paginated, $default): array
    {
        return $default;
    }
}
