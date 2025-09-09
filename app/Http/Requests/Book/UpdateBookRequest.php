<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBookRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'sometimes|required|string|max:255',
            'isbn' => ['sometimes', 'required', 'string', 'unique:books,isbn,except,id'],
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'published_at' => 'nullable|date',
            'total_copies' => 'sometimes|required|integer|min:1',
            'price' => 'sometimes|required|numeric|min:1',
            // 'status' => 'sometimes|required|in:available,unavailable',
            'cover_image' => 'nullable|string',
            'author_id' => 'sometimes|required|exists:authors,id',
        ];
    }
}
