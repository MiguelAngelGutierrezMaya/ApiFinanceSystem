<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class IncomeDetail extends Model
{
    protected $table = 'incomes_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'income_id', 'product_id', 'quantity', 'pruchase_price'
    ];

    public $timestamps = false;

    /**
     * ManyToOne Income
     */
    public function income()
    {
        return $this->belongsTo('App\Income');
    }

    /**
     * ManyToOne Product
     */
    public function product()
    {
        return $this->belongsTo('App\Product');
    }
}
