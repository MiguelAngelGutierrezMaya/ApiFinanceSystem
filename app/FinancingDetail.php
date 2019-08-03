<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FinancingDetail extends Model
{
    protected $table = 'financing_details';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'frecuency_id', 'date_init', 'date_end', 'net_value', 'interest', 'fines', 'discounts', 'addition', 'percentage', 'quotas', 'daily_quota', 'frecuency_quota'
    ];

    public $timestamps = false;

    /**
     * OneToOne Financing
     */
    public function financing()
    {
        return $this->belongsTo('App\Financing', 'foreign_key');
    }

    /**
     * ManyToOne Frecuency
     */
    public function frecuency()
    {
        return $this->belongsTo('App\Frecuency');
    }
}
