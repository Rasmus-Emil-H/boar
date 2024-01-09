<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\middlewares\AuthMiddleware;
use \app\core\src\miscellaneous\CoreFunctions;
use app\models\OrderModel;

class OrderController extends Controller {

    protected string $entity = OrderModel::class;

    public function index() {
        $this->setView('order');
        $this->setData([
            'orders' => CoreFunctions::applicationUser()->orders()->run()
        ]);
    }

    public function edit() {
        if ($this->request->isPost()) $this->crudEntity();
        $this->isViewingValidEntity();
        $this->setView('editOrder');
    }

}