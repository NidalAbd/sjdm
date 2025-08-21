<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProjectRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'client_id' => 'sometimes|required|exists:users,id',
            'service_id' => 'sometimes|required|exists:services,id',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'status' => 'sometimes|required|in:pending,in_progress,completed,cancelled',
            'total_cost' => 'nullable|numeric|min:0',
            'total_duration' => 'nullable|integer|min:0',
            'currency' => 'sometimes|required|string|max:3', // Added currency field
        ];
    }
}
