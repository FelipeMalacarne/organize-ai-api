<?php

namespace App\Http\Requests\Tag;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $tag = $this->route('tag');

        return $this->user()->can('update', $tag);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) {
                    if (\App\Models\Tag::where('user_id', Auth::id())
                        ->where('name', $value)
                        ->where('id', '!=', $this->route('tag')->id)
                        ->exists()) {
                        $fail('The tag name must be unique.');
                    }
                },
            ],
        ];
    }

    /**
     * Customize the error messages for validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => 'The tag name is required.',
            'name.string' => 'The tag name must be a string.',
            'name.max' => 'The tag name must not exceed 255 characters.',
        ];
    }
}
