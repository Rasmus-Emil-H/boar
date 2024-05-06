<?php

/**
 * Language controller
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;

class LanguageController extends Controller {

    public function index() {
        $this->denyPOSTRequest();
    }

    public function maintain() {
        $cLanguage = $this->returnValidEntityIfExists();

        $request = $this->requestBody->body;
        $response = $cLanguage->dispatchHTTPMethod($request->action, $request);

        $this->response->{$this->determineClientResponseMethod(dispatchedHTTPMethodResult: $response)}($response ?? '');
    }

    public function changeSession() {
    	$this->denyPOSTRequest();
        
	    app()->setLanguage($this->requestBody->body->language);

        $this->response->ok();
    }
    
}
