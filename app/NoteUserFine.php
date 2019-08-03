<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteUserFine extends Model
{
    protected $table = 'notes_user_fines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_fine_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }

    /**
     * ManyToOne UserFine
     */
    public function userFine()
    {
        return $this->belongsTo('App\UserFine');
    }
}
