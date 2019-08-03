<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BranchOffice extends Model
{
    protected $table = 'branch_offices';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'nit'
    ];

    public $timestamps = false;

    /**
     * OneToMany to UserWallet
     */
    public function userWallets()
    {
        return $this->hasMany('App\UserWallet');
    }

    /**
     * OneToMany to Financing
     */
    public function financings()
    {
        return $this->hasMany('App\Financing');
    }

    /**
     * OneToMany to FinancialBox
     */
    public function financialBoxes()
    {
        return $this->hasMany('App\FinancialBox');
    }
}
