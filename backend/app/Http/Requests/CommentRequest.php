<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CommentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Le commentaire ne peut pas être vide.',
            'content.max' => 'Le commentaire ne peut pas dépasser 1000 caractères.',
        ];
    }

    public function prepareForValidation(): void
    {
        $content = $this->input('content');

        $this->merge([
            'content' => is_string($content) ? trim($content) : $content,
        ]);
    }
}
