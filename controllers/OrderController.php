<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\gate\Gate;
use \app\core\src\miscellaneous\CoreFunctions;
use app\models\OrderModel;

class OrderController extends Controller {

    protected string $entity = OrderModel::class;

    public function index() {

        if ($this->request->isPost()) $this->response->methodNotAllowed();

        if ($this->request->isGet())
            return $this->setFrontendTemplateAndData(templateFile: 'order', data: [
                'orders' => CoreFunctions::applicationUser()->orders()
            ]);
    }

    public function edit() {
        $cOrder = $this->returnValidEntityIfExists();

        if (!Gate::isAuthenticatedUserAllowed('editOrder', $cOrder)) $this->response->notAllowed();
        
        if ($this->request->isGet())
            return $this->setFrontendTemplateAndData(templateFile: 'editOrder', data: ['cOrder' => $cOrder]);
    }

}