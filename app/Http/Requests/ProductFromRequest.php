<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class ProductFromRequest extends FormRequest
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
        $id     = $this->request->get('id');
        $rules  = [];

        // $rules['parent_category_id']     =  'required';
        // $rules['sub_cat_id']        =  'required';
        // $rules['product_category_id']    =  'required';


        // if($id != ''){
        //     $rules['name']  =  [ 'required', Rule::unique('products')->ignore($id)];
        // }else{
        //     $rules['name']  =  'required|unique:products';
        // }
        $rules['name']      =  'required';
        $rules['price']     =  'required|numeric';

        // $rules['description']   =  'required';
        $rules['uom_id']        =  'required';
        return $rules;
    }
}
