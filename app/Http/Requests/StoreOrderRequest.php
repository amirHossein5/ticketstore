<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email'],
            'quantity' => ['required', 'integer', 'min:1'],
            'card_number' => ['required', 'integer', 'digits:16'],
            'exp_month' => ['required', 'numeric', 'digits:2'],
            'exp_year' => ['required', 'numeric', 'digits:2'],
            'cvc' => ['required', 'numeric', 'digits_between:3,4'],
        ];
    }

    public function after(): array
    {
        return [
            function (Validator $validator) {
                if (request('quantity') > $this->ticket->quantity) {
                    $validator->errors()->add(
                        'quantity',
                        __('validation.max.numeric', [
                            'attribute' => 'quantity',
                            'max' => $this->ticket->quantity
                        ])
                    );
                }
            }
        ];
    }
}
