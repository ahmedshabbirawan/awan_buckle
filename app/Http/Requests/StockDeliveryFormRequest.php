<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Contracts\Validation\Validator;

class StockDeliveryFormRequest extends FormRequest
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




        // $rules['rec_by_name'] = 'required';

        $rules = [
            // 'rec_by_name' => 'required',
            // 'rec_by_designation' => 'required',
            // 'rec_by_cnic' => 'required', 
            // 'rec_by_phone' => 'required', 
            // 'hand_name' => 'required',
            // 'hand_designation' => 'required',
            // 'hand_cnic' => 'required',
            // 'hand_phone' => 'required',


            'purchased_date' => 'required',
        //    'project_id' => 'required',
           //  'project_dg' => 'required', 
           // 'po_loa_loi' => 'required',
           // 'amount_category' => 'required',
        //    'delivery_amount' => 'required',
            'supplier_id' => 'required',
           // 'delivery_challan_no' => 'required',
           // 'warehouse_id' => 'required',
           // 'stock_ledger_reference' => 'required'
        ];

        return $rules;
        
    }
}
