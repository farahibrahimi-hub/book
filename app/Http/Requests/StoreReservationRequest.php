<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreReservationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->is_active;
    }

    public function rules(): array
    {
        return [
            'book_id' => 'required|exists:books,id',
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
