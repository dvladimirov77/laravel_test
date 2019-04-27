<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Cache;
use App;

class OrderController extends Controller
{
    public function index()
    {
		$page = Request::get('page');
		if($page === null) $page = 1;
		
		$on_page = 20;
		
		$orders = App\Order::take($on_page)->skip($on_page * $page - $on_page)->get()->toArray();
		$count_orders = App\Order::count();
		
		$pagination = array(
			'total_count'	=> $count_orders,
			'page'			=> $page,
			'on_page'		=> $on_page,
			'url'			=> '/orders?',
		);
		
		$statuses = array(
			0	=> 'новый',
			10	=> 'подтвержден',
			20	=> 'завершен',
		);
		
		$data = array(
			'orders'		=> $orders,
			'count_orders'	=> $count_orders,
			'statuses'		=> $statuses,
			'pagination'	=> $pagination,
		);
		
		return view('orders', $data);
	}
	
	public function order($order_id)
	{
		$order = App\Order::where('id', '=', $order_id)->first()->toArray();
		
		$partners = App\Partner::pluck('name', 'id')->toArray();
		
		$statuses = array(
			0	=> 'новый',
			10	=> 'подтвержден',
			20	=> 'завершен',
		);
		
		$data = array(
			'order'		=> $order,
			'statuses'	=> $statuses,
			'partners'	=> $partners,
		);
		
		return view('order_form', $data);
	}
	
	public function order_save($order_id)
	{
		$order = App\Order::find($order_id);
		$order->status = Request::post('status');
		$order->client_email = Request::post('client_email');
		$order->partner_id = Request::post('partner');
		
		$order->save();
		
		return redirect('/order/' . $order_id);
	}
}