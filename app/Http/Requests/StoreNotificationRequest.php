<?php

namespace App\Http\Requests;

use App\Enums\NotificationType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreNotificationRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'title' => [
                'required',
                'string',
                'max:255',
                'min:3',
            ],
            'message' => [
                'required',
                'string',
                'max:1000',
                'min:10',
            ],
            'type' => [
                'required',
                Rule::in(NotificationType::values()),
            ],
            'user_id' => [
                'required',
                'integer',
                'exists:users,id',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'title.required' => 'The title is required.',
            'title.min' => 'The title must be at least 3 characters.',
            'title.max' => 'The title may not be greater than 255 characters.',
            'message.required' => 'The message is required.',
            'message.min' => 'The message must be at least 10 characters.',
            'message.max' => 'The message may not be greater than 1000 characters.',
            'type.required' => 'The type is required.',
            'type.in' => 'The type must be one of these: ' . implode(', ', NotificationType::values()) . '.',
            'user_id.required' => 'The user is required.',
            'user_id.exists' => 'User not found.',
        ];
    }
}
