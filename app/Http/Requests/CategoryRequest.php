<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'slug' => ['nullable', 'string', 'max:255', 'regex:/^[a-z0-9-]+$/'],
        ];

        if ($this->isMethod('POST')) {
            $rules['name'][] = Rule::unique('categories');
            $rules['slug'][] = Rule::unique('categories');
        } else {
            $rules['name'][] = Rule::unique('categories')->ignore($this->category->id);
            $rules['slug'][] = Rule::unique('categories')->ignore($this->category->id);
        }

        return $rules;
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The category name is required.',
            'name.string' => 'The category name must be a string.',
            'name.max' => 'The category name cannot exceed 255 characters.',
            'name.unique' => 'This category name is already taken.',
            'description.string' => 'The description must be a string.',
            'slug.unique' => 'This slug is already taken.',
            'slug.regex' => 'The slug may only contain lowercase letters, numbers, and hyphens.',
        ];
    }
} 