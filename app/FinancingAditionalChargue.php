<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancingAditionalChargue extends Model
{
    protected $table = 'financing_aditional_chargues';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financing_id', 'user_id', 'date_aditional_chargue', 'amount', 'state', 'type', 'annulment_detail'
    ];

    /**
     * OneToMany to NoteFinancingAditionalChargue
     */
    public function notesFinancingAditionalChargues()
    {
        return $this->hasMany('App\NoteFinancingAditionalChargue');
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
