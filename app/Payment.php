<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $table = 'payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person_id', 'user_id', 'financing_id', 'financial_visit_id', 'cash_movement_id', 'date_payment', 'state', 'paid_date', 'amount', 'print_number'
    ];

    /**
     * OneToMany to NotePayment
     */
    public function notePayments()
    {
        return $this->hasMany('App\NotePayment');
    }

    /**
     * ManyToOne FinancialVisit
     */
    public function financialVisit()
    {
        return $this->belongsTo('App\FinancialVisit');
    }

    /**
     * ManyToOne Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * ManyToOne Financing
     */
    public function financing()
    {
        return $this->belongsTo('App\Financing');
    }
}
