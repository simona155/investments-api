<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreTransactionRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'account_id' => ['required', 'exists:accounts,id'],
            'type' => ['required', 'in:deposit,withdrawal,buy,sell'],
            'amount' => [
                'required_if:type,deposit,withdrawal',
                'prohibited_if:type,buy,sell',
                'numeric',
                'gt:0',
            ],
            'instrument' => ['required_if:type,buy,sell', 'nullable', 'string'],
            'quantity' => ['required_if:type,buy,sell', 'nullable', 'integer', 'gt:0'],
            'price' => ['required_if:type,buy,sell', 'nullable', 'numeric', 'gt:0'],
        ];
    }
}
