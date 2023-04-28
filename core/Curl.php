<?php

/*******************************
 * Bootstrap Curl 
 * AUTHOR: RE_WEB
 * @package app\core\Curl
*/

namespace app\core;

class curl {

	protected $handler = null;
	protected $url = '';
	protected $info = [];
	protected $data = [];
	protected $headers = [];
	protected $method = 'get';
	protected string $API_KEY = 'MC45NzE3NjM1OTgwMDg4MzUy';
	public $content = '';
	
	public function setUrl(string $url = '') {
		$this->url = $url;
		return $this;
	}
	
	public function setData(array $data = [], bool $jsonEncode = false) {
		$this->data = ( $jsonEncode ? json_encode($data) : $data );
		return $this;
	}
	
	public function setMethod(string $method = 'get') {
		$this->method = $method;
		return $this;
	}
	
	public function setHeaders(array $headers) {
		foreach ( $headers as $header) 
			$this->headers[] = $header;
		return $this;
	}

	public function send(bool $specificDataEntry = false) {
		try {
			if( $this->handler == null ) $this->handler = curl_init();
			switch( strtolower( $this->method ) ) {
				case 'post':
					curl_setopt_array ( $this->handler , [
						CURLOPT_URL => $this->url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_HTTPHEADER => $this->headers,
						CURLOPT_POST => count((array)$this->data),
						CURLOPT_POSTFIELDS => ( $specificDataEntry === false ? $this->data : $this->data[0] ),
					] );
				break;
				default:
					curl_setopt_array ( $this->handler , [
						CURLOPT_URL => $this->url,
						CURLOPT_HTTPHEADER => $this->headers,
						CURLOPT_RETURNTRANSFER => true,
					] );
				break;
			}
			$this->content = curl_exec ( $this->handler );
			$this->info = curl_getinfo( $this->handler );
		} catch( Exception $e ) {
			die( $e->getMessage() );
		}
	}		

	public function close() {
	   curl_close ( $this->handler );
	   $this->handler = null;
	   $this->headers = [];
	   $this->data = [];
	   $this->content = null;
	   $this->info 	  = null;
	}
	
}