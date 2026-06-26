<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;


class StoreTextBrutRequest extends FormRequest
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
            'blueprint_id' => [
               'required',
                Rule::exists('blueprints', 'id')
                    ->where(fn ($query) => $query->where('user_id', $this->user()->id)),
        ],

        'content' => ['required','string','max:5000',],

        ];
    }
}
