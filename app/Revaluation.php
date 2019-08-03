<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Revaluation extends Model
{
    protected $table = 'revaluations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'wallet_id', 'date_revaluation', 'amount', 'state', 'type'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Wallet
     */
    public function wallet()
    {
        return $this->belongsTo('App\Wallet');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
