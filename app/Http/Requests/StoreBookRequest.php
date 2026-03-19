<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBookRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isAdmin() ?? false;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'author' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'cover_image' => ['nullable', 'string', 'max:500'],
            'genre' => ['nullable', 'string', 'max:100'],
            'isbn' => ['nullable', 'string', 'max:20', 'unique:books,isbn'],
            'published_at' => ['nullable', 'date', 'before_or_equal:today'],
        ];
    }
}
