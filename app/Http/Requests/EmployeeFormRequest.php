<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmployeeFormRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(){
        $employeeID = $this->request->get('id');
        $rules =  [
            'first_name' => 'required',
            'last_name' => 'required',
            'project_id' => 'required',
            'designation_id' => 'required',

        ];

        if( empty($employeeID) ){
            $rules['cnic']  = 'required|unique:employees';
        }else{
            $rules['cnic']  = ['required', Rule::unique('employees')->ignore($employeeID)];
        }

        

        return $rules;
    }
}
