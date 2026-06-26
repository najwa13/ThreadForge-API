<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Enum;
use App\Enums\TextBrutStatus;

class UpdateTextBrutRequest extends FormRequest
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
            'blueprint_id' => ['sometimes', 'exists:blueprints,id'],
            'content' => ['sometimes', 'string', 'max:5000'],
            'status' => ['sometimes', new Enum(TextBrutStatus::class)],
        ];
    }
}
