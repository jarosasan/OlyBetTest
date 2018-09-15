<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BalanceTransaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'player_id', 'amount', 'amount_before',
    ];
}
