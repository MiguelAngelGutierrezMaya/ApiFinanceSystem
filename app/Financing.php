<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Financing extends Model
{
    protected $table = 'financings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financing_request_id', 'branch_office_id', 'collector_id', 'financing_request_refinanced_id', 'date_financing', 'total_value', 'balance', 'state', 'exception_state', 'expiration_exception_state', 'rejection_detail'
    ];

    /**
     * OneToOne FinancingDetail
     */
    public function financingDetail()
    {
        return $this->hasOne('App\FinancingDetail');
    }

    /**
     * OneToMany to FinancingCashMovement
     */
    public function financingsCashMovements()
    {
        return $this->hasMany('App\FinancingCashMovement');
    }

    /**
     * OneToMany to Payment
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    /**
     * OneToMany to FinancingAditionalCharge
     */
    public function financingFines()
    {
        return $this->hasMany('App\FinancingAditionalCharge');
    }

    /**
     * OneToMany to FinancialVisit
     */
    public function financialVisits()
    {
        return $this->hasMany('App\FinancialVisit');
    }

    /**
     * ManyToOne BranchOffice
     */
    public function branchOffice()
    {
        return $this->belongsTo('App\BranchOffice');
    }

    /**
     * ManyToOne FinancingRequest
     */
    public function financingRequest()
    {
        return $this->belongsTo('App\FinancingRequest');
    }

    /**
     * ManyToOne User (collector)
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
