<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class OrderProducts extends Model
{
	protected $table = 'order_products';
	
	protected static function boot()
	{
		parent::boot();
		
		static::addGlobalScope(function(Builder $builder) {
			$builder->with('product');
		});
	}
	
	public function product()
	{
		return $this->hasOne(Product::class, 'id', 'product_id');
	}
}