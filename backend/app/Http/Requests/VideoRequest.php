<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class VideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'youtube_id' => 'required|string|max:255',
            'category' => 'required|string|max:255',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre est requis.',
            'youtube_id.required' => 'L\'identifiant ou l\'URL YouTube est requis.',
            'category.required' => 'La catégorie est requise.',
        ];
    }

    public function prepareForValidation(): void
    {
        $title = $this->input('title');
        $youtubeId = $this->input('youtube_id');
        $category = $this->input('category');

        $this->merge([
            'title' => is_string($title) ? trim($title) : $title,
            'youtube_id' => is_string($youtubeId) ? trim($youtubeId) : $youtubeId,
            'category' => is_string($category) ? trim($category) : $category,
        ]);
    }
}
