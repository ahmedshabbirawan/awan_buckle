<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ManagerFormRequest extends FormRequest
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
        $id     = $this->request->get('id');
        $email  = $this->request->get('email');

        $rules  = [];
        if($id != ''){
            $rules['name']  =  [ 'required', Rule::unique('managers')->ignore($id)];
        }else{
            $rules['name']  =  'required|unique:managers';
        }

        // if(){

        // }


        $rules['org_id']  =  'required';
        return $rules;
    }
}
