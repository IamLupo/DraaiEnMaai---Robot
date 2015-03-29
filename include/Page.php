<?php

include_once("Game.php");

class Header {
	const HTML	= 0x0;
	const JSON	= 0x1;
}

class Page {
	public static $domein = "www.minigran.com";
	public static $cookie;
	
	public static $curl_sessie;
	public static $game;
	
	public static function Initialize() {
		Page::$curl_sessie = curl_init();
		
		curl_setopt(Page::$curl_sessie, CURLOPT_COOKIESESSION, true);
		curl_setopt(Page::$curl_sessie, CURLOPT_RETURNTRANSFER, true);
		
		Page::$game = new Game();
	}
	
	function goHome() {
		Page::setHeader(Header::HTML, true);
		curl_setopt(Page::$curl_sessie, CURLOPT_URL, "http://www.minigran.com/draaienmaai/");
		$output = curl_exec(Page::$curl_sessie);

		//Cookie
		Page::setCookieWithHTTP($output);
	}
	
	function Close() {
		curl_close(Page::$curl_sessie);
	}
	
	function getCookieWithHTTP($http) {
			$cookie = "";
		   
			$carr = explode("\n",str_replace("\r\n","\n", $http));
			for($z=0;$z<count($carr);$z++)
			{
					if(preg_match("/set-cookie: (.*)/i",$carr[$z],$cookarr))
					{
							$cookie[] = preg_replace("/expires=(.*)(GMT||UTC)(\S*)$/i","",preg_replace("/path=(.*)/i","",$cookarr[1]));
					}
			}

			for( $i=0; $i<count($cookie); $i++ )
			{
					preg_match("/(\S*)=(\S*)(|;)/", $cookie[$i], $matches);
					$cookie = $matches[1] . "=" . $matches[2];
			}
		   
			return $cookie;
	}
	
	public static function setCookieWithHTTP($http) {
		Page::$cookie = Page::getCookieWithHTTP($http);
		curl_setopt(Page::$curl_sessie, CURLOPT_COOKIE, Page::$cookie);
	}
	
	public static function setHeader($header, $visable) {
		$options = null;
		
		curl_setopt(Page::$curl_sessie, CURLOPT_HEADER, $visable);
		
		switch($header) {
			case Header::HTML:
				$options = array(
									"Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8",
									"Accept-Encoding: gzip, deflate",
									"Accept-Language: en-US,en;q=0.5",
									"Cache-Control: max-age=0",
									"Connection: keep-alive",
									"Host: www.minigran.com",
									"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0"
								);
			break;
			
			case Header::JSON:
				$options = array(
									"Accept: application/json, text/javascript, */*; q=0.01",
									"Accept-Encoding: gzip, deflate",
									"Accept-Language: en-US,en;q=0.5",
									"Connection: keep-alive",
									"Host: www.minigran.com",
									"Referer: http://www.minigran.com/draaienmaai/",
									"User-Agent: Mozilla/5.0 (Windows NT 6.1; WOW64; rv:36.0) Gecko/20100101 Firefox/36.0",
									"X-Requested-With: XMLHttpRequest"
								);
			break;
		}
		if($options != null)
			curl_setopt(Page::$curl_sessie, CURLOPT_HTTPHEADER, $options);
	}
}

?>