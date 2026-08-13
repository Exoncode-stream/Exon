<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ArticleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'content.required' => 'Le contenu est requis.',
        ];
    }

    public function prepareForValidation(): void
    {
        $title = $this->input('title');
        $content = $this->input('content');

        $this->merge([
            'title' => is_string($title) ? trim($title) : $title,
            'content' => is_string($content) ? trim($content) : $content,
        ]);
    }
}
