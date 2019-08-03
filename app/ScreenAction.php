<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ScreenAction extends Model
{
    protected $table = 'screen_actions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'screen_id', 'action_id'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Action
     */
    public function action()
    {
        return $this->belongsTo('App\Action');
    }

    /**
     * ManyToOne Screen
     */
    public function screen()
    {
        return $this->belongsTo('App\Screen');
    }

    /**
     * OneToMany UserScreenAction
     */
    public function userScreenActions()
    {
        return $this->hasMany('App\UserScreenAction');
    }
}
