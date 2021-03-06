<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stake_amount', 'created_at',
    ];
}
