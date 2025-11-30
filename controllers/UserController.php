<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;

class UserController extends Controller {

    public function index() {
        $this->denyPOSTRequest();
        $this->response->ok(CoreFunctions::applicationUser()?->key());
    }

    public function profile() {
        if ($this->request->isGet()) {
            return $this->setFrontendTemplateAndData('profile', ['user' => appUser()]);
        }

        $this->response->ok();
    }

}
