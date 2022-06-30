<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class RegisterReq extends FormRequest
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
     * @return array
     */
    public function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
            'name' => 'required',
            'fcm_token' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'email.required' => 'An Email must be provided',
            'password.required' => 'A Password must be provided',
            'name.required' => 'A Name must be provided',
            'fcm_token.required' => 'A Fcm_token must be provided',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['isDone' => false, 'data' => $validator->errors(), 'message' => "validation error"], 200));
    }
}
