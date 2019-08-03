<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'category_id', 'name', 'description', 'price', 'stock', 'state'
    ];

    public $timestamps = false;

    /**
     * OneToMany to FinancingRequest
     */
    public function financingRequests()
    {
        return $this->hasMany('App\FinancingRequest');
    }

    /**
     * OneToMany to IncomeDetail
     */
    public function incomeDetails()
    {
        return $this->hasMany('App\IncomeDetail');
    }

    /**
     * ManyToOne Category
     */
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
}
