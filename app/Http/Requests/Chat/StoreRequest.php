<?php

namespace App\Http\Requests\Chat;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'content'   => ['required', 'string', 'max:2000'],
            'user_name' => ['nullable', 'string', 'max:50'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $data = [];

        if ($this->has('content')) {
            $data['content'] = trim((string) $this->input('content'));
        }

        if ($this->has('user_name')) {
            $data['user_name'] = trim((string) $this->input('user_name'));
        }

        $this->merge($data);
    }
}
