<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Screen extends Model
{
    protected $table = 'screens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'names', 'description'
    ];

    public $timestamps = false;

    /**
     * OneToMany to UserScreen
     */
    public function userScreens()
    {
        return $this->hasMany('App\UserScreen');
    }

    /**
     * OneToMany to RolScreen
     */
    public function rolScreens()
    {
        return $this->hasMany('App\RolScreen');
    }

    /**
     * OneToMany to ScreenAction
     */
    public function screenActions()
    {
        return $this->hasMany('App\ScreenAction');
    }
}
