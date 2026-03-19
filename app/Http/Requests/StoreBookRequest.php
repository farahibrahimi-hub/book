<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'author' => 'required|string|max:255',
            'description' => 'nullable|string',
            'cover_image' => 'nullable|image|max:2048',
            'genre' => 'required|string|max:255',
            'isbn' => 'nullable|string|max:50|unique:books,isbn',
            'available_copies' => 'required|integer|min:0',
            'total_copies' => 'required|integer|min:1',
            'published_at' => 'nullable|date',
        ];
    }
}
