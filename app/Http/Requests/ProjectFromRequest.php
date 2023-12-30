<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProjectFromRequest extends FormRequest
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
        $id     = $this->request->get('id');
        $rules  = [];
        if($id != ''){
            $rules['name']  =  [ 'required', Rule::unique('projects')->ignore($id)];
        }else{
            $rules['name']  =  'required|unique:projects';
        }

        if($id != ''){
            $rules['code']  =  [ 'required', Rule::unique('projects')->ignore($id)];
        }else{
            $rules['code']  =  'required|unique:projects';
        }

        
        $rules['manager_id']  =  'required';
        $rules['org_id']  =  'required';
        return $rules;
    }
}
