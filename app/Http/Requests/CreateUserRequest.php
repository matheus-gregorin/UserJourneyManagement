<?php

namespace App\Http\Requests;

use Domain\Enums\CodesEnum;
use App\Http\Responses\ApiResponse;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
            'name' => 'required|max:50',
            'email' => 'required|max:50|email|unique:usuarios,email',
            'password' => 'required|size:8',
            'phone' => 'required|unique:usuarios,phone|size:13',
            'is_admin' => 'required|boolean',
            'role' => 'required'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(
            ApiResponse::error(
                [    
                    $validator->errors()
                ],
                CodesEnum::messageValidationError,
                CodesEnum::codeErrorUnprocessableEntity
            )
        );
    }
}
