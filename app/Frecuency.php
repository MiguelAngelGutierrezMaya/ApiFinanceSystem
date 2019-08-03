<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Frecuency extends Model
{
    protected $table = 'frecuencies';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'days', 'description'
    ];

    public $timestamps = false;

    /**
     * OneToMany to FinancingDetail
     */
    public function financingDetails()
    {
        return $this->hasMany('App\FinancingDetail');
    }

    /**
     * OneToMany to LaborInformation
     */
    public function laborInformations()
    {
        return $this->hasMany('App\LaborInformation');
    }
}
