<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
	protected static function boot()
	{
		parent::boot();
		
		static::addGlobalScope(function(Builder $builder) {
			$builder->with('vendor');
		});
	}
	
	public function vendor()
	{
		return $this->hasOne(Vendor::class, 'id', 'vendor_id');
	}
}