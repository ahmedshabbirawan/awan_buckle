<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

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

    protected function failedValidation(Validator $validator) {
        throw new HttpResponseException(response()->json(['status'=>'failed',
                                                    'message'=>$validator->errors()->first(),
                                                    'errors'=>$validator->errors()->all()], 422));
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
        if($id != ''){
            $rules['mobile']  =  [ 'required', Rule::unique('customers')->ignore($id)];
        }else{
            $rules['mobile']  =  'required|unique:customers';
        }
        // $rules['address']  =  'required';
        // $rules['phone']  =  'required';
        // $rules['city_id']  =  'required';
        // if($email != ''){
        //     $rules['email']  =  'email';
        // }
        
        return $rules;
    }
}
