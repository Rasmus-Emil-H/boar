<?php

/**
 * Bootstrap View
 * AUTHOR: RE_WEB
 * @package app\core\View
*/

namespace app\core;

use app\core\exceptions\NotFoundException;
use app\core\File;

class View {

  protected string $partialsDir      = '/views/partials/';
  protected const TPL_FILE_EXTENSION = '.tpl.php';

  protected File $fileHandler;

  public function __construct() {
    $this->fileHandler = new File();
  }

  public function renderView() {
    require_once Application::$app->controller->getView(); 
  }

  public function getTemplate(string $template): string {
    $templateFile = Application::$ROOT_DIR . $this->partialsDir . $template . self::TPL_FILE_EXTENSION;
    if (!file_exists($templateFile)) throw new NotFoundException();
    return $templateFile;
  }

}
