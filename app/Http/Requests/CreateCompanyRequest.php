<?php

namespace App\Http\Requests;

use Domain\Enums\CodesEnum;
use App\Http\Responses\ApiResponse;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class CreateCompanyRequest extends FormRequest
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
            'corporate_reason' => 'required|string|max:50|unique:company,corporate_reason',
            'fantasy_name' => 'nullable|max:50',
            'cnpj' => 'required|size:14|unique:company,cnpj|regex:/^\d{14}$/',
            'plan' => 'required|string|in:basic,premium',
            'active' => 'required|boolean'
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        $messageErrors = "";
        foreach ($validator->errors()->messages() as $key => $error) {
            $messageErrors = $error[0];
        }

        throw new HttpResponseException(
            ApiResponse::error(
                [
                    $messageErrors
                ],
                CodesEnum::messageValidationError,
                CodesEnum::codeErrorUnprocessableEntity
            )
        );
    }
}
