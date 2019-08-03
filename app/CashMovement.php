<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CashMovement extends Model
{
    protected $table = 'cash_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'movement_reason_id', 'financial_box_id', 'cash_movement_id_transfer', 'date_cash_movement', 'amount', 'state', 'description'
    ];

    /**
     * OneToMany to NoteCashMovement
     */
    public function notesCashMovements()
    {
        return $this->hasMany('App\NoteCashMovement');
    }

    /**
     * OneToMany to FinancingCashMovement
     */
    public function financingsCashMovements()
    {
        return $this->hasMany('App\FinancingCashMovement');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * ManyToOne MovementReason
     */
    public function movementReason()
    {
        return $this->belongsTo('App\MovementReason');
    }

    /**
     * ManyToOne FinancialBox
     */
    public function financialBox()
    {
        return $this->belongsTo('App\FinancialBox');
    }
}
