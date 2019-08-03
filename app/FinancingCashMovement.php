<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancingCashMovement extends Model
{
    protected $table = 'financings_cash_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financing_id', 'cash_movement_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Financing
     */
    public function financing()
    {
        return $this->belongsTo('App\Financing');
    }

    /**
     * ManyToOne CashMovement
     */
    public function cashMovement()
    {
        return $this->belongsTo('App\CashMovement');
    }
}
