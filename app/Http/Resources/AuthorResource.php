<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AuthorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'name' => strtoupper($this->name),
            'bio' => $this->bio,
            'nationality' => strtolower($this->nationality),
            'books_count' => $this->whenCounted('books'),
            'books' => BookResource::collection($this->whenLoaded('books')),
        ];
    }
}
