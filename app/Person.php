<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Person extends Model
{
    use Notifiable;

    protected $table = 'persons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_type_id', 'business_charge_id', 'location_id', 'document_number', 'names', 'surnames', 'phone_number', 'cell_phone_number', 'email', 'qualification', 'rating_detail', 'state'
    ];

    /**
     * OneToOne User
     */
    public function user()
    {
        return $this->hasOne('App\User');
    }

    /**
     * ManyToOne DocumentType
     */
    public function documentType()
    {
        return $this->belongsTo('App\DocumentType');
    }

    /**
     * ManyToOne BusinessCharge
     */
    public function businessCharge()
    {
        return $this->belongsTo('App\BusinessCharge');
    }

    /**
     * ManyToOne Location
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }

    /**
     * OneToMany LaborInformation
     */
    public function laborInformations()
    {
        return $this->hasMany('App\LaborInformation');
    }

    /**
     * OneToMany ReferencePerson
     */
    public function referencePersons()
    {
        return $this->hasMany('App\ReferencePerson');
    }

    /**
     * OneToMany Payment
     */
    public function payments()
    {
        return $this->hasMany('App\Payment');
    }

    /**
     * OneToMany FinancingRequest
     */
    public function clients()
    {
        return $this->hasMany('App\FinancingRequest');
    }

    /**
     * OneToMany FinancingRequest
     */
    public function deptors()
    {
        return $this->hasMany('App\FinancingRequest');
    }
}
