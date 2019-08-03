<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserScreen extends Model
{
    protected $table = 'user_screens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'screen_id', 'user_id', 'state'
    ];

    public $timestamps = false;

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * ManyToOne Screen
     */
    public function screen()
    {
        return $this->belongsTo('App\Screen');
    }
}
