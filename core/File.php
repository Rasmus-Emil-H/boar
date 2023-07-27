<?php

/**
 * File handler 
 * AUTHOR: RE_WEB
 * @package app\core\File
*/

namespace app\core;

use app\models\FileModel;
use app\core\Application;

class File extends FileModel {

    public const INVALID_EXTENSION = 'Invalid file extension';
    public const INVALID_FILE_NAME = 'Invalid file name';
    protected const TPL_FILE_EXTENSION = '.tpl.php';

    /** 
     * @var string $currentString
    */

    public string $fileName;

    public function getUploadedFile() {
        return $_FILES['file']['name'] ?? 'invalid';
    }
    
    public function getCurrentFiles() {
        return $_FILES;
    }

    public function moveFile(): bool {
        if ( !$this->checkFileType() ) throw new \Exception(self::INVALID_EXTENSION);
        if ( !$this->checkFileName() ) throw new \Exception(self::INVALID_FILE_NAME);
        return move_uploaded_file(sys_get_temp_dir(), Application::UPLOAD_FOLDER);
    }

    protected function checkFileType(): bool {
        return in_array(filetype($this->getUploadedFile()), $this->allowedFileExtensions);
    }

    public function checkFileName(): bool {
        return Application::$app->regex->match('/a-zA-Z0-9/', $this->fileName);
    }

    public function unlinkFile(): bool {
        return unlink(Application::UPLOAD_FOLDER . $this->fileName);
    }

    public function getFile() {
        if ( !file_exists(Application::UPLOAD_FOLDER . $this->fileName)) throw new \Exception('File not found');
    }
    
    public function fileSize(string $type, string $quality) {
        
    }

    public function requireApplicationFile(string $folder, string $file): void {
        require_once Application::$ROOT_DIR . $folder . $file . self::TPL_FILE_EXTENSION;
    }

}