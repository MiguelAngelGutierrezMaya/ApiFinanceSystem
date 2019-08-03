<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Note extends Model
{
    protected $table = 'notes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_note', 'description'
    ];

    /**
     * OneToMany to NoteCashMovement
     */
    public function notesCashMovements()
    {
        return $this->hasMany('App\NoteCashMovement');
    }

    /**
     * OneToMany to NoteFinancingAditionalChargue
     */
    public function notesFinancingAditionalChargues()
    {
        return $this->hasMany('App\NoteFinancingAditionalChargue');
    }

    /**
     * OneToMany to NoteFinancialVisit
     */
    public function notesFinancialVisits()
    {
        return $this->hasMany('App\NoteFinancialVisit');
    }

    /**
     * OneToMany to NotePayment
     */
    public function notesPayments()
    {
        return $this->hasMany('App\NotePayment');
    }

    /**
     * OneToMany to NoteUserFine
     */
    public function notesUserFines()
    {
        return $this->hasMany('App\NoteUserFine');
    }

    /**
     * OneToMany to NoteUserCommission
     */
    public function notesUserCommissions()
    {
        return $this->hasMany('App\NoteUserCommission');
    }
}
