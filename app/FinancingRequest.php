<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancingRequest extends Model
{
    protected $table = 'financing_requests';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'client_id', 'deptor_id', 'second_deptor_id', 'adviser_id', 'product_id', 'date_financing_requests', 'description', 'state', 'amount', 'refinancing_request', 'amount_refinancing_request', 'rejection_detail', 'quantity'
    ];

    /**
     * OneToMany to Financing
     */
    public function financings()
    {
        return $this->hasMany('App\Financing');
    }

    /**
     * ManyToOne Person
     */
    public function client()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * ManyToOne Person
     */
    public function deptor()
    {
        return $this->belongsTo('App\Person');
    }

    /**
     * ManyToOne User
     */
    public function adviser()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * ManyToOne to Product
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
