<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NoteFinancing extends Model
{
    protected $table = 'notes_financings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'financing_id', 'note_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Financing
     */
    public function financing()
    {
        return $this->belongsTo('App\Financing');
    }

    /**
     * ManyToOne Note
     */
    public function note()
    {
        return $this->belongsTo('App\Note');
    }
}
