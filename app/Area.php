<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'areas';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'city_id', 'name', 'commune'
    ];

    public $timestamps = false;

    /**
     * ManyToOne City
     */
    public function city()
    {
        return $this->belongsTo('App\City');
    }

    /**
     * OneToMany Location
     */
    public function locations()
    {
        return $this->hasMany('App\Location');
    }
}
