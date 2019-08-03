<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotePayment extends Model
{
    protected $table = 'notes_payments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'payment_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Payment
     */
    public function payment()
    {
        return $this->belongsTo('App\Payment');
    }

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
