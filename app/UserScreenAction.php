<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserScreenAction extends Model
{
    protected $table = 'user_screen_actions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'screen_action_id', 'user_id', 'state'
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
     * ManyToOne ScreenAction
     */
    public function screenAction()
    {
        return $this->belongsTo('App\ScreenAction');
    }
}
