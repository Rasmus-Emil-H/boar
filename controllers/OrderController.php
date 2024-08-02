<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\gate\Gate;
use \app\core\src\html\table\Header;
use \app\core\src\miscellaneous\CoreFunctions;

class OrderController extends Controller {

    public function index() {

        $this->denyPOSTRequest();

        if ($this->request->isGet())
            return $this->setFrontendTemplateAndData(templateFile: 'order', data: [
                'orders' => CoreFunctions::applicationUser()->orders() ?? [],
                'tableHeader' => (new Header(['#' => '#', 'Total' => 'Total']))->create()
            ]);
    }

    public function edit() {
        $cOrder = $this->returnValidEntityIfExists();

        // if (!Gate::isAuthenticatedUserAllowed('editOrder', $cOrder)) $this->response->notAllowed();
        
        if ($this->request->isGet())
            return $this->setFrontendTemplateAndData(templateFile: 'editOrder', data: ['cOrder' => $cOrder]);
    }

}