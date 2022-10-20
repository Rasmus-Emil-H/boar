<?php

/*******************************
 * Bootstrap Curl 
 * AUTHOR: RE_WEB
 * @package app\core\Curl
*/

namespace app\core;

class curl {

	protected $handler  = null;
	protected $url 		= '';
	protected $info 	= [];
	protected $data 	= [];
	protected $headers  = [];
	protected $method   = 'get';
	protected $content  = '';
	
	public function setUrl(string $url = '') {
		$this->url = $url;
		return $this;
	}
	
	public function setData(array $data = []) {
		$this->data = $data;
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

	public function send() {
		try{
			if( $this->handler == null ) $this->handler = curl_init( );
			switch( $this->method ) {
				case 'post':
					curl_setopt_array ( $this->handler , [
						CURLOPT_URL => $this->url,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_HTTPHEADER => $this->headers,
						CURLOPT_POST => count($this->data),
						CURLOPT_POSTFIELDS => http_build_query($this->data),
					] );
				break;           
				default:
					curl_setopt_array ( $this->handler , [
						CURLOPT_URL => $this->url,
						CURLOPT_RETURNTRANSFER => true,
					] );
				break;
			}
			$this->content = curl_exec ( $this->handler );
			$this->info = curl_getinfo( $this->handler );
		} catch( \Exception $e ) {
			die( $e->getMessage() );
		}
	}

	public function close() {
	   curl_close ( $this->handler );
	   $this->handler = null;
	}
	
}