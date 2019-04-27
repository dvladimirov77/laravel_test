<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App;

class TemperatureController extends Controller
{
    public function index()
    {
		$url = "https://api.weather.yandex.ru/v1/forecast?lat=53.2520900&lon=34.3716700&lang=ru_RU";
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('X-Yandex-API-Key: 40656c9e-b752-45db-848c-6e1fcee18d3f'));
		curl_setopt($ch, CURLOPT_NOBODY, false);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:47.0) Gecko/20100101 Firefox/47.0');
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_ENCODING, 'gzip,deflate');
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		curl_setopt($ch, CURLOPT_VERBOSE, true);
		
		$result = curl_exec($ch);
		$data = json_decode($result);
		curl_close($ch);
		
		$data = array(
			'data'			=> $data,
		);
		
		return view('temperature', $data);
	}
}