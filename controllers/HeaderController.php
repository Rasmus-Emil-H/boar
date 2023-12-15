<?php

/**
 * Header Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/  

namespace app\controllers;

use app\core\Controller;

class HeaderController extends Controller {

    public function index() {
		$this->setChildren(['navbar' => 'DOMNode:navbar']);
		$this->setView('partials/', 'header');
		$this->setData([
			'header' => $this->getView(), 
			'appName' => app()->config->get('appName'),
			'stylesheets' => app()->clientAssets->get('css'),
			'metaTags' => app()->clientAssets->get('metaTags')
		]);
    }

}