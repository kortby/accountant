<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class TaxInformationRequest extends FormRequest
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
        $rules = [
            'user_id' => [
                Rule::requiredIf($this->user()->role === 'accountant' || $this->user()->role === 'admin'),
                'nullable',
                'exists:users,id',
            ],
            'ssn' => ['required', 'string', 'regex:/^\d{3}-\d{2}-\d{4}$/'],
            'date_of_birth' => ['required', 'date', 'before:today'],
            'occupation' => ['required', 'string', 'max:255'],
            'marital_status' => ['required'],
            'address' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'size:2'],
            'zip_code' => ['required', 'string', 'regex:/^\d{5}(-\d{4})?$/'],
            'tax_year' => ['required', 'integer', 'min:2020', 'max:'.(date('Y') + 1)],
            'income_types' => ['required', 'array', 'min:1'],
            'income_types.*' => ['required', Rule::in(['w2', '1099_nec', '1099_int', '1099_div', 'business', 'rental', 'retirement', 'other'])],
            'notes' => ['nullable', 'string', 'max:1000'],

            // Dependent validation
            'dependents' => ['nullable', 'array'],
            'dependents.*.first_name' => ['required', 'string', 'max:255'],
            'dependents.*.last_name' => ['required', 'string', 'max:255'],
            'dependents.*.social_security_number' => ['required', 'string'],
            'dependents.*.relationship' => ['required'],
            'dependents.*.date_of_birth' => ['required', 'date', 'before:today'],

            // Document validation
            'documents' => ['nullable', 'array'],
            'documents.*' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
        ];

        return $rules;
    }
}
