<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ReferencePerson extends Model
{
    protected $table = 'references_persons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'reference_id', 'person_id'
    ];

    /**
     * ManyToOne Reference
     */
    public function reference()
    {
        return $this->belongsTo('App\Reference');
    }

    /**
     * ManyToOne Person
     */
    public function person()
    {
        return $this->belongsTo('App\Person');
    }
}
