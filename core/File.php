<?php

/**
 * File handler 
 * AUTHOR: RE_WEB
 * @package app\core\File
*/

namespace app\core;

use app\models\FileModel;

use app\core\exceptions\NotFoundException;

class File extends FileModel {

    public const INVALID_EXTENSION = 'Invalid file extension';
    public const INVALID_FILE_NAME = 'Invalid file name';
    protected const TPL_FILE_EXTENSION = '.tpl.php';
    protected const FILE_NOT_FOUND = 'File not found';

    /** 
     * @var string $currentString
    */

    public string $fileName;

    public function getUploadedFile(): string {
        return $_FILES['file']['name'] ?? self::FILE_NOT_FOUND;
    }
    
    public function getCurrentFiles() {
        return $_FILES;
    }

    public function moveFile(): bool {
        if ( !$this->checkFileType() ) throw new \Exception(self::INVALID_EXTENSION);
        if ( !$this->checkFileName() ) throw new \Exception(self::INVALID_FILE_NAME);
        return move_uploaded_file(sys_get_temp_dir(), app()::UPLOAD_FOLDER);
    }

    protected function checkFileType(): bool {
        return in_array(filetype($this->getUploadedFile()), $this->allowedFileExtensions);
    }

    public function checkFileName(): bool {
        return app()->regex->match('/a-zA-Z0-9/', $this->fileName);
    }

    public function unlinkFile(): bool {
        return unlink(app()::UPLOAD_FOLDER . $this->fileName);
    }

    public function getFile() {
        if ( !file_exists(app()::UPLOAD_FOLDER . $this->fileName)) throw new \Exception(self::FILE_NOT_FOUND);
    }
    
    public function fileSize(string $type, string $quality) {
        
    }

    public function exists($file) {
      if(!file_exists($file)) throw new NotFoundException();
    }

    public function requireApplicationFile(string $folder, string $file, array $params = []): void {
      $file = app()::$ROOT_DIR . $folder . $file . self::TPL_FILE_EXTENSION;
      $this->exists($file);
      require_once $file;
    }

}
