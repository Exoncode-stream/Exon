<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LinkRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'url' => 'required|url|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du lien est requis.',
            'url.required' => 'L\'URL du lien est requise.',
            'url.url' => 'L\'URL fournie doit être une adresse valide (ex: https://example.com).',
        ];
    }

    public function prepareForValidation(): void
    {
        $name = $this->input('name');
        $url = $this->input('url');

        $this->merge([
            'name' => is_string($name) ? trim($name) : $name,
            'url' => is_string($url) ? trim($url) : $url,
        ]);
    }
}
