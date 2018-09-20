<?php

namespace App\Http\Controllers;

use App\BalanceTransaction;
use App\Bet;
use App\BetSelection;
use App\Http\Requests\StoreBetRequest;
use App\Player;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;


class BetsController extends Controller
{

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return JsonResponse|Response
     */
    public function store(Request $request)
    {
        //Get max win
        $winAmount = $request['stake_amount'];
        foreach ($request['selections'] as $selection) {
            $winAmount= $winAmount  * $selection['odds'];
        }
        $request['win_amount'] = $winAmount;

        //Player balance
        $player = Player::find($request['player_id']);

        //If player don't exist, create new player
        if (empty($player)) {
            $player = Player::create(['id' => $request['player_id']]);
        }
        $balance = $player->balance;
        $request['balance'] = $balance;

        //Validate Request
        $storeBetRequest = new StoreBetRequest;

        if ($storeBetRequest->errorsRespons($request)){
            return response()->json($storeBetRequest->errorsRespons($request))->setStatusCode(Response::HTTP_BAD_REQUEST);
        }

        //Random sleep after validation
         sleep(rand(0, 30));

            $player = Player::find($request['player_id']);

            //If player don't exist, create new player
            if (empty($player)) {
                $player = Player::create(['id' => $request['player_id']]);
            }

            //Create new bet
            $bet = Bet::create(['stake_amount' => $request['stake_amount']]);

            //Store bet selections
            for ($i = 0; $i < count($request['selections']); $i++) {
                BetSelection::create(['bet_id' => $bet->id, 'selection_id' => $request['selections'][$i]['id'], 'odds' => $request['selections'][$i]['odds']]);
            }

            //Create Balance transaction
            $playerTransaction = BalanceTransaction::where('player_id', $player->id)->first();
            if (empty($playerTransaction)) {
                BalanceTransaction::create(['player_id' => $player->id, 'amount' => round($winAmount, 2), 'amount_before' => 0]);
            } else {
                $amountBefore = $playerTransaction->amount;
                BalanceTransaction::create(['player_id' => $player->id, 'amount' => round($winAmount, 2), 'amount_before' => $amountBefore]);
            }

            //Update Player balance
            $player->update(['balance' => $player->balance + round($winAmount, 2)]);

            return response()->json()->setStatusCode(Response::HTTP_CREATED);

    }

}
