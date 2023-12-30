<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CustomerFromRequest extends FormRequest
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
        $id         = $this->request->get('id');
      //  $email      = $this->request->get('email');
        $rules  = [];
        $rules['name']  =  'required';
        // if($id != ''){
        //     $rules['mobile']  =  [ 'required', Rule::unique('customer')->ignore($id)];
        // }else{
        //     $rules['mobile']  =  'required|unique:shops';
        // }
        // $rules['address']  =  'required';
        // $rules['phone']  =  'required';
        // $rules['city_id']  =  'required';
        // if($email != ''){
        //     $rules['email']  =  'email';
        // }
        
        return $rules;
    }
}
