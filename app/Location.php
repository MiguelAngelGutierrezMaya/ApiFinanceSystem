<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $table = 'locations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'area_id', 'latitude', 'longitude', 'housing_type', 'collection_address', 'address'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Area
     */
    public function area()
    {
        return $this->belongsTo('App\Area');
    }

    /**
     * OneToMany Reference
     */
    public function references()
    {
        return $this->hasMany('App\Reference');
    }

    /**
     * OneToMany FinancialVisit
     */
    public function financialVisits()
    {
        return $this->hasMany('App\FinancialVisit');
    }
}
