<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteUserCommission extends Model
{
    protected $table = 'notes_user_commissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_commission_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne UserCommission
     */
    public function userCommission()
    {
        return $this->belongsTo('App\UserCommission');
    }

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
