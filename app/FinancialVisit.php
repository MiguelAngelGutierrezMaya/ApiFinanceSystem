<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancialVisit extends Model
{
    protected $table = 'financial_visits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financing_id', 'user_id', 'location_id', 'date_financial_visit'
    ];

    /**
     * OneToMany to NoteFinancialVisit
     */
    public function notesFinancialVisits()
    {
        return $this->hasMany('App\NoteFinancialVisit');
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

    /**
     * ManyToOne Location
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    /**
     * OneToMany Payment
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }
}
