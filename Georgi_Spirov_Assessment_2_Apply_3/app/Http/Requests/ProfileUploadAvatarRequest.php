<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProfileUploadAvatarRequest extends FormRequest
{
	public function rules(): array
	{
        return [
            /**
             * The avatar is required, must be an image file with:
             * - mime type: jpg, jpeg, png, webp
             * - maximum file size: 2MB
             */
            'avatar' => [
                'required',
                'image',
                'mimes:jpg,jpeg,png,webp',
                'max:2048'
            ],
        ];
	}

	public function authorize(): bool
	{
        return $this->user() !== null;
    }
}
