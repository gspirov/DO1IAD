<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UploadProjectImagesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     * Only users with the "update" permission on the project can upload images.
     */
    public function authorize(): bool
    {
        $project = $this->route('project');

        return $this->user()->can('update', $project);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * An array of images is required.
             * Each image must be an image file with:
             * - mime type: jpg, jpeg, png, webp
             * - maximum file size: 2MB
             */
            'images' => ['required', 'array'],
            'images.*' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ];
    }

    public function attributes(): array
    {
        return [
            /**
             * Instead of showing for each uploaded file the attribute as "image.{index}", we want to show just "image".
             */
            'images.*' => 'image'
        ];
    }
}
