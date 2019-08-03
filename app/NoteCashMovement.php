<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteCashMovement extends Model
{
    protected $table = 'notes_cash_movements';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cash_movement_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne CashMovement
     */
    public function cashMovement()
    {
        return $this->belongsTo('App\CashMovement');
    }

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
