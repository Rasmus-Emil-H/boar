<?php

/**
 * Language controller
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\I18n;

class TranslationController extends Controller {

    public function index() {
        $this->denyPOSTRequest();
    }

    public function translate() {
        $this->denyPOSTRequest();
        $request = $this->requestBody->body;

        $this->response->ok((new I18n())->translate($request->translation));
    }

    public function maintain() {
        $cTranslation = $this->returnValidEntityIfExists();

        $request = $this->requestBody->body;
        $response = $cTranslation->dispatchHTTPMethod($request->action, $request);

        $this->response->{$this->determineClientResponseMethod(dispatchedHTTPMethodResult: $response)}($response ?? '');
    }

    public function edit() {

        $cTranslation = $this->returnValidEntityIfExists();

        $request = $this->requestBody->body;
        $response = $cTranslation->dispatchHTTPMethod($request->action, $request);

        if ($this->request->isGet())
            $this->response->customOKResponse('html', $response);

        $this->response->ok();

    }
    
}