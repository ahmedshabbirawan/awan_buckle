<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StockDeliveryProductFormRequest extends FormRequest
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
      $isExpiryDate = $this->request->get('is_expiry_date');
      if($isExpiryDate != ''){
        $rules['warranty_date']  =  'required';
      } 
        $rules['shop_id']         =  'required';    
        $rules['product_id']      =  'required';
        $rules['qty']             =  'required';
        $rules['unit_price']      =  'required';
        $rules['sub_cat_id']      =  'required';
        $rules['product_cat_id']  =  'required';
        return $rules;
    }
    protected function failedValidation(Validator $validator) {
      throw new HttpResponseException(response()->json(['status'=>'failed',
                                                  'message'=>$validator->errors()->first(),
                                                  'errors'=>$validator->errors()->all()], 422));
  }
}
