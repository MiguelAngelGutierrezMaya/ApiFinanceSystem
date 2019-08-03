<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    protected $table = 'incomes';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'date_income', 'total_value', 'state'
    ];

    /**
     * OneToMany to IncomeDetail
     */
    public function incomeDetails()
    {
        return $this->hasMany('App\IncomeDetail');
    }

    /**
     * ManyToOne User
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
