<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserCommission extends Model
{
    protected $table = 'user_commissions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'date_user_commission', 'user_id', 'amount'
    ];

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * OneToMany to NoteUserCommission
     */
    public function notesUserCommissions()
    {
        return $this->hasMany('App\NoteUserCommission');
    }
}
