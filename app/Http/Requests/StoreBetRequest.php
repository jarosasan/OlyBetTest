<?php

namespace App\Http\Requests;

use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;

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
            'player_id'         => 'required',
            'stake_amount'      => 'required|regex:/^\d+(\.\d{1,2})?$/|numeric|min:0.3|max:10000',
            'selections'        => 'required|array|min:1|max:20',
            'selections.*.id'   => 'required|distinct|numeric',
            'selections.*.odds' => 'required|min:1|max:10000|regex:/^\d+(\.\d{1,3})?$/',
            'win_amount'        => 'numeric|max:20000',
        ];
    }


    public function messages()
    {
        return [
            'player.required'               => ['code'=>1, 'message'=>'Betslip structure mismatch'],
            'stake_amount.required'         => ['code'=>1, 'message'=>'Betslip structure mismatch'],
            'stake_amount.regex'            => ['code'=>0, 'message'=>'Unknown error'],
            'selections.required'           => ['code'=>1, 'message'=>'Betslip structure mismatch'],
            'selections.min'                => ['code'=>4, 'message'=>'Minimum number of selections is :min'],
            'selections.max'                => ['code'=>5, 'message'=>'Maximum number of selections is :max'],
            'selections.*.id.required'      => ['code'=>5, 'message'=>'Minimum number of selections is :min'],
            'selections.*.id.numeric'       => ['code'=>0, 'message'=>'Unknown error'],
            'selections.*.id.distinct'      => ['code'=>8, 'message'=>'Duplicate selections found'],
            'selections.*.odds.required'    => ['code'=>1, 'message'=>'Betslip structure mismatch'],
            'selections.*.odds.min'         => ['code'=>6, 'message'=>'Minimum odds are :min'],
            'selections.*.odds.max'         => ['code'=>7, 'message'=>'Maximum odds are :max'],
            'selections.*.odds.regex'       => ['code'=>0, 'message'=>'Unknown error'],
            'selections.*.odds.numeric'     => ['code'=>0, 'message'=>'Unknown error'],
        ];
    }

    public function errorsRespons( $request)
    {
        $valid = Validator::make($request->all(), $this->rules(), $this->messages());

        //Validate Player Balance
        $balanceValid = Validator::make($request->all(), [
            'balance' => 'integer|min:'.$request['stake_amount'].'',
        ]);

        if ($valid->fails() || $balanceValid->fails()) {
            $errorMessages = [];
            foreach ($balanceValid->errors()->messages() as $k => $v) {
                    $errorMessages[] = $v[0];
            }

            //Get global error messages
            foreach ($request->all() as $key => $value) {
                foreach ($valid->errors()->messages() as $k => $v) {
                    if ($k == $key) {
                        $errorMessages[] = $v[0];
                    }
                }
            }



            //Get selections error messages
            for ($i = 0; $i < count($request['selections']); $i++) {
                $t = $request['selections'][$i];
 ;               foreach ($valid->errors()->messages() as $k => $v) {
                    if ($k == 'selections.' . $i . '.id' || $k == 'selections.' . $i . '.odds') {
                        if (!empty($v)) {
                            $t['errors'] = $v;
                        }
                    }
                }
                $selections[] = $t;
            }

            //Create collection to response
            if (count($errorMessages) > 0) {
                $collection = collect([
                    'player_id' => $request['player_id'],
                    'stake_amount' => $request['stake_amount'],
                    'errors' => $errorMessages,
                    'selections' => $selections
                ]);
            } else {
                $collection = collect([
                    'player_id' => $request['player_id'],
                    'stake_amount' => $request['stake_amount'],
                    'selections' => $selections
                ]);
            }
            return $collection;
        }
        return false;
    }
}
