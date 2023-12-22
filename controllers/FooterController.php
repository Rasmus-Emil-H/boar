<?php

/**
 * Header Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use \app\core\src\Controller;

class FooterController extends Controller {

	public function index() {
		$this->setView('footer', 'partials/');
		$this->setData([
			'footer' => $this->getView(), 
			'js' => app()->getClientAssets()->get('js')
		]);
	}

}