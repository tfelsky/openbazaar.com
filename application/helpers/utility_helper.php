<?php

function asset_url()
{
	return base_url() . 'assets/';
}

function market_price($coinType)
{
	$ticker = file_get_contents("https://ticker.openbazaar.org/api");
	$ticker = json_decode($ticker);
	return "Ƀ" . number_format(1 / $ticker->{$coinType}->last * $ticker->BTC->last, 5);
}

function convert_price($amount, $from, $to, $precision = 8)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$ob_ticker = $CI->cache->get('ob_ticker');
	if ($ob_ticker == "") {
		$ob_ticker = json_decode(file_get_contents("https://ticker.openbazaar.org/api"));
	}

	if ($to != "BTC" || isset($ob_ticker->$to)) {
		$precision = 2;
	}

	$e = $CI->cache->file->save('ob_ticker', $ob_ticker, 300);
	if ($from == "BTC") {
		if (!isset($ob_ticker->$to)) {
			$to = "BTC";
		}

		$price = ($amount / 100000000) * $ob_ticker->$to->last;
	}
	else {
		$price = (($amount / 100) * $ob_ticker->$from->last) / $ob_ticker->$to->last;
	}

	return $price;
}

function get_market_price($coinType, $precision = 8)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$ob_ticker = $CI->cache->get('ob_ticker');
	if ($ob_ticker == "") {
		$ob_ticker = json_decode(file_get_contents("https://ticker.openbazaar.org/api"));
	}

	$e = $CI->cache->file->save('ob_ticker', $ob_ticker, 300);
	return $ob_ticker->$coinType->last;
}

function get_http_response_code($url)
{
	$headers = get_headers($url);
	return substr($headers[0], 9, 3);
}

function get_profile($peerID)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$profile_load = $CI->cache->get('profile_' . $peerID);
	if ($profile_load == "") {
		/*
		if(get_http_response_code("https://gateway.ob1.io/ipns/".$peerID."/profile.json") != "200"){
		$profile_load = "{}";
		}else{
		*/
		$ctx = stream_context_create(array(
			'http' => array(
				'timeout' => 1
			)
		));
		$profile_load = @file_get_contents("https://gateway.ob1.io/ipns/" . $peerID . "/profile.json", 0, $ctx);

		// 	    }

		$CI->cache->file->save('profile_' . $peerID, $profile_load, 900); // 15 minutes cache
	}

	return json_decode($profile_load);
}

function get_listings($peerID)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$load = $CI->cache->get('listings_' . $peerID);
	if ($load == "") {
		$ctx = stream_context_create(array(
			'http' => array(
				'timeout' => 1
			)
		));
		$load = @file_get_contents("https://gateway.ob1.io/ipns/" . $peerID . "/listings.json", 0, $ctx);
		if($load != "") {
			$CI->cache->file->save('listings_' . $peerID, $load, 900); // 15 minutes cache
		}		
	}
	
	$load = ($load == "") ? "[]" : $load;

	return json_decode($load);
}

function get_listing($peerID, $slug)
{
	$CI = & get_instance();
	$CI->load->driver('cache', array(
		'adapter' => 'apc',
		'backup' => 'file'
	));
	$listing_load = $CI->cache->get('listing_' . $slug);
	if ($listing_load == "") {
		$listing_load = @file_get_contents("https://gateway.ob1.io/ipns/" . $peerID . "/listings/" . $slug . ".json");
		$CI->cache->file->save('listing_' . $slug, $listing_load, 5400); // 60 minutes cache
	}

	return json_decode($listing_load);
}

function contract_type_to_friendly($type)
{
	switch ($type) {
	case "PHYSICAL_GOOD":
		return "Physical Good";
		break;

	case "DIGITAL_GOOD":
		return "Digital Good";
		break;

	case "SERVICE":
		return "Service";
		break;

	case "CRYPTOCURRENCY":
		return "Cryptocurrency";
		break;

	default:
		return "";
	}
}

function condition_to_friendly($condition)
{
	switch ($condition) {
	case "NEW":
		return "New";
		break;

	case "USED_EXCELLENT":
		return "Used - Excellent";
		break;

	case "USED_GOOD":
		return "Used - Good";
		break;

	case "USED_POOR":
		return "Used - Poor";
		break;

	case "REFURBISHED":
		return "Refurbished";
		break;

	default:
		return "";
	}
}

function set_new_url($url_params, $name, $value)
{

	// if(isset($url_params[$name])) {

	$url_params[$name] = urlencode($value);

	// }

	$URI = http_build_query($url_params);
	$uri_parts = explode('?', $_SERVER['REQUEST_URI'], 2);
	return "//$_SERVER[HTTP_HOST]$uri_parts[0]?$URI";
}

function get_language()
{
	$locale = locale_accept_from_http($_SERVER['HTTP_ACCEPT_LANGUAGE']);
	echo $locale;
}

function ticker_to_symbol($ticker)
{
	$currency_symbols = array(
		'AED' => '&#1583;.&#1573;', // ?
		'BTC' => 'Ƀ',
		'AFN' => '&#65;&#102;',
		'ALL' => '&#76;&#101;&#107;',
		'AMD' => '',
		'ANG' => '&#402;',
		'AOA' => '&#75;&#122;', // ?
		'ARS' => '&#36;',
		'AUD' => '&#36;',
		'AWG' => '&#402;',
		'AZN' => '&#1084;&#1072;&#1085;',
		'BAM' => '&#75;&#77;',
		'BBD' => '&#36;',
		'BDT' => '&#2547;', // ?
		'BGN' => '&#1083;&#1074;',
		'BHD' => '.&#1583;.&#1576;', // ?
		'BIF' => '&#70;&#66;&#117;', // ?
		'BMD' => '&#36;',
		'BND' => '&#36;',
		'BOB' => '&#36;&#98;',
		'BRL' => '&#82;&#36;',
		'BSD' => '&#36;',
		'BTN' => '&#78;&#117;&#46;', // ?
		'BWP' => '&#80;',
		'BYR' => '&#112;&#46;',
		'BZD' => '&#66;&#90;&#36;',
		'CAD' => '&#36;',
		'CDF' => '&#70;&#67;',
		'CHF' => '&#67;&#72;&#70;',
		'CLF' => '', // ?
		'CLP' => '&#36;',
		'CNY' => '&#165;',
		'COP' => '&#36;',
		'CRC' => '&#8353;',
		'CUP' => '&#8396;',
		'CVE' => '&#36;', // ?
		'CZK' => '&#75;&#269;',
		'DJF' => '&#70;&#100;&#106;', // ?
		'DKK' => '&#107;&#114;',
		'DOP' => '&#82;&#68;&#36;',
		'DZD' => '&#1583;&#1580;', // ?
		'EGP' => '&#163;',
		'ETB' => '&#66;&#114;',
		'EUR' => '&#8364;',
		'FJD' => '&#36;',
		'FKP' => '&#163;',
		'GBP' => '&#163;',
		'GEL' => '&#4314;', // ?
		'GHS' => '&#162;',
		'GIP' => '&#163;',
		'GMD' => '&#68;', // ?
		'GNF' => '&#70;&#71;', // ?
		'GTQ' => '&#81;',
		'GYD' => '&#36;',
		'HKD' => '&#36;',
		'HNL' => '&#76;',
		'HRK' => '&#107;&#110;',
		'HTG' => '&#71;', // ?
		'HUF' => '&#70;&#116;',
		'IDR' => '&#82;&#112;',
		'ILS' => '&#8362;',
		'INR' => '&#8377;',
		'IQD' => '&#1593;.&#1583;', // ?
		'IRR' => '&#65020;',
		'ISK' => '&#107;&#114;',
		'JEP' => '&#163;',
		'JMD' => '&#74;&#36;',
		'JOD' => '&#74;&#68;', // ?
		'JPY' => '&#165;',
		'KES' => '&#75;&#83;&#104;', // ?
		'KGS' => '&#1083;&#1074;',
		'KHR' => '&#6107;',
		'KMF' => '&#67;&#70;', // ?
		'KPW' => '&#8361;',
		'KRW' => '&#8361;',
		'KWD' => '&#1583;.&#1603;', // ?
		'KYD' => '&#36;',
		'KZT' => '&#1083;&#1074;',
		'LAK' => '&#8365;',
		'LBP' => '&#163;',
		'LKR' => '&#8360;',
		'LRD' => '&#36;',
		'LSL' => '&#76;', // ?
		'LTL' => '&#76;&#116;',
		'LVL' => '&#76;&#115;',
		'LYD' => '&#1604;.&#1583;', // ?
		'MAD' => '&#1583;.&#1605;.', //?
		'MDL' => '&#76;',
		'MGA' => '&#65;&#114;', // ?
		'MKD' => '&#1076;&#1077;&#1085;',
		'MMK' => '&#75;',
		'MNT' => '&#8366;',
		'MOP' => '&#77;&#79;&#80;&#36;', // ?
		'MRO' => '&#85;&#77;', // ?
		'MUR' => '&#8360;', // ?
		'MVR' => '.&#1923;', // ?
		'MWK' => '&#77;&#75;',
		'MXN' => '&#36;',
		'MYR' => '&#82;&#77;',
		'MZN' => '&#77;&#84;',
		'NAD' => '&#36;',
		'NGN' => '&#8358;',
		'NIO' => '&#67;&#36;',
		'NOK' => '&#107;&#114;',
		'NPR' => '&#8360;',
		'NZD' => '&#36;',
		'OMR' => '&#65020;',
		'PAB' => '&#66;&#47;&#46;',
		'PEN' => '&#83;&#47;&#46;',
		'PGK' => '&#75;', // ?
		'PHP' => '&#8369;',
		'PKR' => '&#8360;',
		'PLN' => '&#122;&#322;',
		'PYG' => '&#71;&#115;',
		'QAR' => '&#65020;',
		'RON' => '&#108;&#101;&#105;',
		'RSD' => '&#1044;&#1080;&#1085;&#46;',
		'RUB' => '&#1088;&#1091;&#1073;',
		'RWF' => '&#1585;.&#1587;',
		'SAR' => '&#65020;',
		'SBD' => '&#36;',
		'SCR' => '&#8360;',
		'SDG' => '&#163;', // ?
		'SEK' => '&#107;&#114;',
		'SGD' => '&#36;',
		'SHP' => '&#163;',
		'SLL' => '&#76;&#101;', // ?
		'SOS' => '&#83;',
		'SRD' => '&#36;',
		'STD' => '&#68;&#98;', // ?
		'SVC' => '&#36;',
		'SYP' => '&#163;',
		'SZL' => '&#76;', // ?
		'THB' => '&#3647;',
		'TJS' => '&#84;&#74;&#83;', // ? TJS (guess)
		'TMT' => '&#109;',
		'TND' => '&#1583;.&#1578;',
		'TOP' => '&#84;&#36;',
		'TRY' => '&#8356;', // New Turkey Lira (old symbol used)
		'TTD' => '&#36;',
		'TWD' => '&#78;&#84;&#36;',
		'TZS' => '',
		'UAH' => '&#8372;',
		'UGX' => '&#85;&#83;&#104;',
		'USD' => '&#36;',
		'UYU' => '&#36;&#85;',
		'UZS' => '&#1083;&#1074;',
		'VEF' => '&#66;&#115;',
		'VND' => '&#8363;',
		'VUV' => '&#86;&#84;',
		'WST' => '&#87;&#83;&#36;',
		'XAF' => '&#70;&#67;&#70;&#65;',
		'XCD' => '&#36;',
		'XDR' => '',
		'XOF' => '',
		'XPF' => '&#70;',
		'YER' => '&#65020;',
		'ZAR' => '&#82;',
		'ZMK' => '&#90;&#75;', // ?
		'ZWL' => '&#90;&#36;',
	);
	if (!array_key_exists($ticker, $currency_symbols)) {
		return $currency_symbols["BTC"];
	}
	else {
		return $currency_symbols[$ticker];
	}
}

function pretty_price($price, $currency)
{
	$user_currency = (isset($_COOKIE['currency'])) ? $_COOKIE['currency'] : "BTC";
	$symbol = ticker_to_symbol($currency);
	$user_symbol = ticker_to_symbol($user_currency);

	if ($user_currency != "BTC") {
		$amount = money_format('%n', convert_price($price, $currency, $user_currency));
		return $user_symbol . number_format($amount, 2);
	}
	else {
		$amount = preg_replace('/0{1,2}$/', '', number_format(convert_price($price / 100000000, $currency, $user_currency) , 8));
		return $user_symbol . number_format($amount, 2);
	}
}

function sani_input($url) {
	return filter_var($url, FILTER_SANITIZE_STRING);
}