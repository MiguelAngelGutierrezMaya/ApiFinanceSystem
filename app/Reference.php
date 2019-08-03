<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Reference extends Model
{
    protected $table = 'references';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'location_id', 'type', 'names', 'surnames', 'phone_number', 'cell_phone_number'
    ];

    public $timestamps = false;

    /**
     * OneToMany ReferencePerson
     */
    public function referencePersons()
    {
        return $this->hasMany('App\ReferencePerson');
    }

    /**
     * ManyToOne Location
     */
    public function location()
    {
        return $this->belongsTo('App\Location');
    }
}
