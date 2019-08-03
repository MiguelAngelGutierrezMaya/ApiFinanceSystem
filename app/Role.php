<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $table = 'roles';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name'
    ];

    public $timestamps = false;

    /**
     * OneToMany to User
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }

    /**
     * OneToMany to RolScreen
     */
    public function rolScreens()
    {
        return $this->hasMany('App\RolScreen');
    }
}
