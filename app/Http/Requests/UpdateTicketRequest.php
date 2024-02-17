<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTicketRequest extends FormRequest
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
            'title' => ['required', 'max:255'],
            'subtitle' => ['required', 'max:255'],
            'price' => ['required', 'numeric', 'min:1'],
            'quantity' => ['required', 'integer', 'min:1'],
            'time_to_use' => ['required', 'date'],
            'image' => ['nullable', 'image', 'max:1024', Rule::dimensions()->minWidth(400)->ratio(5 / 3)],
        ];
    }

    public function messages(): array
    {
        return [
            'price.min' => __('Price must be greater than one dollars.'),
        ];
    }
}
