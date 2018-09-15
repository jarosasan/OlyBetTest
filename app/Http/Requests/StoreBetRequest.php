<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;

class StoreBetRequest extends FormRequest
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
            'payer_id'          => 'required',
            'stake_amount'      => 'numeric|required|regex:/^(\b\d{1,6})*(\.\d{1.2})?\b/|min:0.3|max:10000',
            'selections'        => 'required',
            'selections.*.id'   => 'required|min:1|max:20|numeric|distinct',
            'selections.*.odds' => 'numeric|required|min:1|max:10000',
        ];
    }


    public function messages()
    {
        return [
            'payer.required'             => 'Betslip structure mismatch',
            'stake_amount.required'      => 'Minimum stake amount is :min',
            'stake_amount.min'           => 'Minimum stake amount is :min',
            'stake_amount.max'           => 'Maximum stake amount is :max',
            'selections.required'        => 'Betslip structure mismatch',
            'selections.*.id.required'   => 'Minimum number of selections is :min',
            'selections.*.id.min'        => 'Minimum number of selections is :min',
            'selections.*.id.max'        => 'Maximum number of selections is :max',
            'selections.*.id.distinct'   => 'Duplicate selections found',
            'selections.*.odds.required' => 'Minimum odds are :min',
            'selections.*.odds.min'      => 'Minimum odds are :min',
            'selections.*.odds.max'      => 'Maximum odds are :max',
        ];
    }






}
