<?php

/**
 * Webhook Controller 
 * 
 * Used for real time event driven data exchange
 * 
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;

class WebhookController extends Controller {

    public function index() {
        return $this->response->ok('Hello from ' . __CLASS__);
    }

}