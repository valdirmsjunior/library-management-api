<?php

namespace App\Http\Requests\Book;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'isbn' => ['required', 'string', 'max:13', 'unique:books,isbn'],
            'description' => 'nullable|string',
            'genre' => 'nullable|string',
            'published_at' => 'nullable|date',
            'total_copies' => 'required|integer|min:1',
            'price' => 'required|numeric|min:1',
            // 'status' => 'required|in:available,unavailable',
            'cover_image' => 'nullable|string',
            'author_id' => 'required|exists:authors,id',
        ];
    }
}
