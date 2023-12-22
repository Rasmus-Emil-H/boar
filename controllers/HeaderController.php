<?php

/**
 * Header Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
 */  

namespace app\controllers;

use \app\core\Controller;

class HeaderController extends Controller {

    public function index() {
		$this->setView('header', 'partials/');
		$this->setChildren(['navbar' => 'DOMNode:navbar']);
		$assets = app()->getClientAssets();
		$this->setData([
			'appName' => app()->getConfig()->get('appName'),
			'stylesheets' => $assets->get('css'),
			'metaTags' => $assets->get('metaTags')
		]);
    }

}