<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'cities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'department_id', 'name'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Department
     */
    public function department()
    {
        return $this->belongsTo('App\Department');
    }

    /**
     * OneToMany Area
     */
    public function areas()
    {
        return $this->hasMany('App\Area');
    }
}
