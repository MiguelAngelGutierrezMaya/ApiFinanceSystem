<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_wallet', 'amount'
    ];

    /**
     * OneToMany to UserWallet
     */
    public function userWallets()
    {
        return $this->hasMany('App\UserWallet');
    }

    /**
     * OneToMany to Revaluation
     */
    public function revaluations()
    {
        return $this->hasMany('App\Revaluation');
    }
}
