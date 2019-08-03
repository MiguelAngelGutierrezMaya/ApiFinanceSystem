<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RolScreen extends Model
{
    protected $table = 'rol_screens';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'screen_id', 'rol_id', 'state'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Role
     */
    public function role()
    {
        return $this->belongsTo('App\Role');
    }

    /**
     * ManyToOne Screen
     */
    public function screen()
    {
        return $this->belongsTo('App\Screen');
    }
}
