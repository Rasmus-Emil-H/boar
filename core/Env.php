<?php

/*******************************
 * Bootstrap Env
 * Temp storage for additional keys
 * AUTHOR: RE_WEB
 * @package app\core\Env
*/

namespace app\core;

class Env {

    public string $trackingAPI = 'https://tracking.autologik.dk/fetch';
    public string $trackingAPIKey = 'MC45NzE3NjM1OTgwMDg4MzUy';
    
    public string $bookAPI = 'https://dev.book.autologik.dk/api';
    public string $bookAPIKey = 'MC45NzE3NjM1OTgwMDg4MzUy';

    public function get(string $key): string {
        return $this->{$key};
    }

}