<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;
use App;

class ProductsController extends Controller
{
    public function index()
    {
		$page = Request::get('page');
		if($page === null) $page = 1;
		
		$on_page = 25;
		
		$products = App\Product::take($on_page)->skip($on_page * $page - $on_page)->get()->toArray();
		$count_products = App\Product::count();
		
		$pagination = array(
			'total_count'	=> $count_products,
			'page'			=> $page,
			'on_page'		=> $on_page,
			'url'			=> '/products?',
		);
		
		$data = array(
			'products'			=> $products,
			'count_products'	=> $count_products,
			'pagination'		=> $pagination,
		);
		
		return view('products', $data);
	}
	
	public function set_price()
	{
		$order = App\Product::find(Request::post('id'));
		$order->price = Request::post('price');
		
		$order->save();
		
		return response()->json([
			'name' => 'Abigail'
		]);
	}
}