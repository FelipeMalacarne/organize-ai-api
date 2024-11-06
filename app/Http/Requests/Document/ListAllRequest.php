<?php

namespace App\Http\Requests\Document;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class ListAllRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'limit' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
            'title' => ['string', 'max:255'],
            'tag' => ['string', 'exists:tags,name'],
            'order_by' => ['string', 'in:created_at,title,updated_at,id'],
            'order' => ['string', 'in:asc,desc'],
        ];
    }
}
