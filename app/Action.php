<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Action extends Model
{
    protected $table = 'actions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'visual_name', 'description'
    ];

    public $timestamps = false;

    /**
     * OneToMany to ScreenAction
     */
    public function screenAction()
    {
        return $this->hasMany('App\ScreenAction');
    }
}
