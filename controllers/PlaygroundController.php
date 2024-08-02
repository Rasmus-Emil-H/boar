<?php

/**
 * Language controller
 * AUTHOR: RE_WEB
 * @package app\controllers
 */

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;

class PlaygroundController extends Controller {

    public function index() {

        // if (!CoreFunctions::applicationUser()->isAdmin()) $this->response->redirect(app()->getConfig()->get('routes')->defaults->redirectTo);

        if ($this->request->isGet()) 
            return $this->setFrontendTemplateAndData('playground');

        if (!isset($this->requestBody->body->playgroundKey) || $this->requestBody->body->playgroundKey !== app()->getConfig()->get('playgroundKey')) $this->response->notAllowed();

        $debug = $this->requestBody->body->Input;
        ob_start();
        eval($debug);
        $safe = ob_get_clean();

        $this->response->customOKResponse('php', $safe);
    }
    
}
