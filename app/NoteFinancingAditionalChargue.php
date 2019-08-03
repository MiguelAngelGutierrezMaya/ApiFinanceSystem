<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteFinancingAditionalChargue extends Model
{
    protected $table = 'notes_financing_ad_ch';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financing_aditional_chargue_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne FinancingAditionalCharge
     */
    public function financingAditionalCharge()
    {
        return $this->belongsTo('App\FinancingAditionalCharge');
    }

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
