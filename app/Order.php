<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
	protected static function boot()
	{
		parent::boot();
		
		static::addGlobalScope(function(Builder $builder) {
			$builder->with('partner');
		});
		
		static::addGlobalScope(function(Builder $builder) {
			$builder->with('order_products');
		});
	}
	
	public function partner()
	{
		return $this->hasOne(Partner::class, 'id', 'partner_id');
	}
	
	public function order_products()
	{
		return $this->hasMany(OrderProducts::class, 'order_id', 'id');
	}
	
}