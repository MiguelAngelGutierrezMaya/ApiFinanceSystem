<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'description', 'state'
    ];

    public $timestamps = false;

    /**
     * OneToMany to Product
     */
    public function products()
    {
        return $this->hasMany('App\Product');
    }
}
