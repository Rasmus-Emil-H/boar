<?php

/**
 * Bootstrap View
 * AUTHOR: RE_WEB
 * @package app\core\View
 */

namespace app\core\src;

use \app\core\src\exceptions\NotFoundException;
use \app\core\src\miscellaneous\CoreFunctions;

final class View {

    protected string $partialsDir      = '/views/partials/';
    protected const TPL_FILE_EXTENSION = '.tpl.php';
    public const INVALID_VIEW = 'Invalid view';

    public function getTemplatePath(string $template, string $dir): string {
        return CoreFunctions::app()::$ROOT_DIR .  File::VIEWS_FOLDER . $dir . $template . File::TPL_FILE_EXTENSION;
    }

}
