<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DocumentType extends Model
{
    protected $table = 'document_types';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type', 'name', 'acronym'
    ];

    public $timestamps = false;

    /**
     * OneToMany Person
     */
    public function persons()
    {
        return $this->hasMany('App\Person');
    }
}
