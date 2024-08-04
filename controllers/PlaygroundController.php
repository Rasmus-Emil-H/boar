<?php

/**
 * Initial draft for a playground directly from the frontend
 * 
 * Use with extreme caution
 * Feel free to further strengthen the allowence of the controller access
 */

/**
 * Playground controller
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\gate\Gate;

class PlaygroundController extends Controller {

    public function index() {

        if (!Gate::canInteractWith('playground', $this->requestBody))
            $this->response->redirect(app()->getConfig()->get('routes')->defaults->redirectTo);

        if ($this->request->isGet()) 
            return $this->setFrontendTemplateAndData('playground');

        $debug = $this->requestBody->body->Input;
        ob_start();
        eval($debug);
        $safe = ob_get_clean();

        $this->response->customOKResponse('php', $safe);
    }
    
}