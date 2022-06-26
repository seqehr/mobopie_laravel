<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class AddBusinessReq extends FormRequest
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
            'name' => 'required',
            'bio' => 'required',
            'img' => 'required',
            'bg' => 'required',
            'cat_id' => 'required',
            'lat' => 'required',
            'lon' => 'required',
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'A name must be provided',
            'bio.required' => 'A bio must be provided',
            'img.required' => 'A img must be provided',
            'bg.required' => 'A bg must be provided',
            'cat_id.required' => 'a cat id must be provided',
            'lat.required' => 'a lattiude  must be provided',
            'lon.required' => 'a longttiude  must be provided',
        ];
    }
    protected function failedValidation(Validator $validator)
    {
        throw new HttpResponseException(response()->json(['isDone' => false, 'data' => $validator->errors(), 'message' => "validation error"], 422));
    }
}
