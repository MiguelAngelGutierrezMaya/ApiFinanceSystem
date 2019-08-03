<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class BusinessCharge extends Model
{
    protected $table = 'business_charges';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description'
    ];

    /**
     * OneToMany Person
     */
    public function persons()
    {
        return $this->hasMany('App\Person');
    }
}
