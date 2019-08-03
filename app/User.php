<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Passport\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, Notifiable;

    protected $table = 'users';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'email', 'last_session', 'last_activity', 'last_ip', 'last_req_pass', 'passreset_code', 'state', 'api_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * ManyToOne Role
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    /**
     * OneToOne Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person', 'foreign_key');
    }

    /**
     * OneToMany Password_reset
     */
    public function password_resets()
    {
        return $this->hasMany('App\Password_reset');
    }

    /**
     * OneToMany UserCommission
     */
    public function userCommissions()
    {
        return $this->hasMany('App\UserCommission');
    }

    /**
     * OneToMany UserScreen
     */
    public function userScreens()
    {
        return $this->hasMany('App\UserScreen');
    }

    /**
     * OneToMany UserScreenAction
     */
    public function userScreenActions()
    {
        return $this->hasMany('App\UserScreenAction');
    }

    /**
     * OneToMany FinancialBox
     */
    public function financialBoxes()
    {
        return $this->hasMany('App\FinancialBox');
    }

    /**
     * OneToMany Payment
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    /**
     * OneToMany UserFine
     */
    public function userFines()
    {
        return $this->hasMany('App\UserFine');
    }

    /**
     * OneToMany Financing
     */
    public function financings()
    {
        return $this->hasMany('App\Financing');
    }

    /**
     * OneToMany FinancingRequest
     */
    public function financingRequests()
    {
        return $this->hasMany('App\FinancingRequest');
    }

    /**
     * OneToMany FinancingAditionalCharge
     */
    public function financingFines()
    {
        return $this->hasMany('App\FinancingAditionalCharge');
    }

    /**
     * OneToMany FinancialVisit
     */
    public function financialVisits()
    {
        return $this->hasMany('App\FinancialVisit');
    }

    /**
     * OneToMany Income
     */
    public function incomes()
    {
        return $this->hasMany('App\Income');
    }

    /**
     * OneToMany UserWallet
     */
    public function usersWallets()
    {
        return $this->hasMany('App\UserWallet');
    }

    /**
     * OneToMany CashMovement
     */
    public function cashMovements()
    {
        return $this->hasMany('App\CashMovement');
    }

    /**
     * OneToMany to Revaluation
     */
    public function revaluations()
    {
        return $this->hasMany('App\Revaluation');
    }
}
