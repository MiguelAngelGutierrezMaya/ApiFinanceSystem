<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
    protected $table = 'users_wallets';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'wallet_id', 'branch_office_id', 'state'
    ];

    public $timestamps = false;

    /**
     * ManyToOne UserWallet
     */
    public function userWallet()
    {
        return $this->belongsTo('App\UserWallet');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * ManyToOne BranchOffice
     */
    public function branchOffice()
    {
        return $this->belongsTo('App\BranchOffice');
    }
}
