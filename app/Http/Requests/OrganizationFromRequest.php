<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OrganizationFromRequest extends FormRequest
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
    public function rules(){

        $id = $this->request->get('id');

        $rules = [];

        if($id != ''){
            $rules['name']  =  [ 'required', Rule::unique('organizations')->ignore($id)];
        }else{
            $rules['name']  =  'required|unique:organizations';
        }

        return $rules;
    }
}
