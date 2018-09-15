<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'balance',
    ];
}
