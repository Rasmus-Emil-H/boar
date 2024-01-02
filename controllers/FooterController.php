<?php

/**
 * Header Controller
 * AUTHOR: RE_WEB
 * @package app\controllers
*/

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;

class FooterController extends Controller {

	public function index() {
		$this->setView('footer', 'partials/');
		$this->setData([
			'footer' => $this->getView(), 
			'js' => CoreFunctions::app()->getParentController()->getClientAssets()->get('js')
		]);
	}

}