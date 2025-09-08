<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BookResource extends JsonResource
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
            'title' => $this->title,
            'isbn' => $this->isbn,
            'description' => $this->description,
            'genre' => $this->genre,
            'published_date' => $this->published_date,
            'total_copies' => $this->total_copies,
            'available_copies' => $this->available_copies,
            'price' => $this->price,
            'status' => $this->status,
            'cover_image' => $this->cover_image,
            'is_available' => $this->isAvailable(),
            'author' => new AuthorResource($this->whenLoaded('author')),
        ];
    }
}
