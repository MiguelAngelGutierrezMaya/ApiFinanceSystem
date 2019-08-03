<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MovementReason extends Model
{
    protected $table = 'movement_reasons';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'movement_type', 'name', 'description', 'state'
    ];

    public $timestamps = false;

    /**
     * OneToMany to CashMovement
     */
    public function cashMovements()
    {
        return $this->hasMany('App\CashMovement');
    }
}
