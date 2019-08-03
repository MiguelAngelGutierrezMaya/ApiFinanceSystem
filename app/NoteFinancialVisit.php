<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteFinancialVisit extends Model
{
    protected $table = 'notes_financial_visits';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financial_visit_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne FinancialVisit
     */
    public function financialVisit()
    {
        return $this->belongsTo('App\FinancialVisit');
    }

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
