<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    protected $table = 'departments';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'country_id', 'name'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Country
     */
    public function country()
    {
        return $this->belongsTo('App\Country');
    }

    /**
     * OneToMany City
     */
    public function cities()
    {
        return $this->hasMany('App\City');
    }
}
