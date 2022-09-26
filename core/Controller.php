<?php

/*******************************
 * Bootstrap Controller 
 * AUTHOR: RE_WEB
 * @package app\core\Controller
*/

namespace app\core;

class Controller {

    public string $layout = 'main';

    public function render(string $view, array $params = array()) {
        return Application::$app->router->renderView($view, $params);
    }

    public function setLayout(string $layout) {
        $this->layout = $layout;
    }

}