<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRoleRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }


    public function rules()
    {
        return [
            'name' => 'sometimes|required|string|max:255|unique:roles,name,' . $this->role->id,
            'permissions' => 'array',
            'permissions.*' => 'string|exists:permissions,name',
        ];
    }
}
