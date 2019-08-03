<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LaborInformation extends Model
{
    protected $table = 'labor_informations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'person_id', 'frecuency_id', 'current_condition', 'charge', 'company_name', 'address',
        'first_telephone_number', 'second_telephone_number', 'contract_type', 'antiquity', 'salary'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * ManyToOne Frecuency
     */
    public function frecuency()
    {
        return $this->belongsTo('App\Frecuency');
    }
}
