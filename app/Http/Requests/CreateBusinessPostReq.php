<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class CreateBusinessPostReq extends FormRequest
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
            'title' => 'required',
            'caption' => 'required',
            'category' => 'required',
            'price' => 'required',
            'inputs' => 'required',
        ];
    }

    public function messages()
    {
        return [
            'title.required' => 'A title must be provided',
            'caption.required' => 'A caption must be provided',
            'category.required' => 'A category must be provided',
            'price.required' => 'A price must be provided',
            'inputs.required' => 'File inputs must be provided',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['isDone' => false, 'data' => $validator->errors(), 'message' => "validation error"], 422));
    }
}
