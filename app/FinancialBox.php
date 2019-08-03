<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialBox extends Model
{
    protected $table = 'financial_boxes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'branch_office_id', 'date_financial_box', 'name', 'amount', 'state'
    ];

    /**
     * OneToMany to CashMovement
     */
    public function cashMovements()
    {
        return $this->hasMany('App\CashMovement');
    }

    /**
     * ManyToOne BranchOffice
     */
    public function branchOffice()
    {
        return $this->belongsTo('App\BranchOffice');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
