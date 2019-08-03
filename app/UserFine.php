<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserFine extends Model
{
    protected $table = 'user_fines';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date_user_fine', 'amount', 'state'
    ];

    /**
     * OneToMany to NoteUserFine
     */
    public function notesUserFines()
    {
        return $this->hasMany('App\NoteUserFine');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
