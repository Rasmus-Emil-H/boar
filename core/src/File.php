<?php

/**
 * File handler 
 * AUTHOR: RE_WEB
 * @package app\core
 */

namespace app\core\src;

use \app\core\src\exceptions\NotFoundException;
use \app\core\src\miscellaneous\CoreFunctions;

final class File {

    protected array $allowedFileExtensions = ['jpg', 'jpeg', 'webp', 'png'];

    public const INVALID_EXTENSION  = 'Invalid file extension';
    public const INVALID_FILE_NAME  = 'Invalid file name';
    public const TPL_FILE_EXTENSION = '.tpl.php';
    public const VIEWS_FOLDER       = '/views/';

    protected const FILE_NOT_FOUND     = 'File not found';
    protected const UPLOAD_FOLDER      = __DIR__.'/uploads/';

    public function __construct(
        public string $fileName
    ) {
        
    }

    public function get(): bool|string {
        return file_get_contents($this->fileName);
    }

    public function getUploadedFile(): string {
        return $this->getCurrentlyUploadedFiles()[$this->fileName] ?? self::FILE_NOT_FOUND;
    }
    
    public function getCurrentlyUploadedFiles() {
        return CoreFunctions::app()->getRequest()->clientRequest->files;
    }

    public function moveFile(): bool {
        if (!$this->checkFileType()) throw new \Exception(self::INVALID_EXTENSION);
        if (!$this->validateFileName()) throw new \Exception(self::INVALID_FILE_NAME);
        return move_uploaded_file(sys_get_temp_dir(), self::UPLOAD_FOLDER);
    }

    protected function checkFileType(): bool {
        return in_array(filetype($this->getUploadedFile()), $this->allowedFileExtensions);
    }

    public function validateFileName(): bool {
        return preg_match('/a-zA-Z0-9/', $this->fileName);
    }

    public function unlinkFile(): bool {
        return unlink(self::UPLOAD_FOLDER . $this->fileName);
    }

    public function getFile() {
        if (!file_exists(self::UPLOAD_FOLDER . $this->fileName)) throw new \Exception(self::FILE_NOT_FOUND);
    }

    public function exists() {
      if (!file_exists($this->fileName)) throw new NotFoundException();
    }

    public function requireApplicationFile(string $folder, string $file, array $params = []): void {
      $file = CoreFunctions::app()::$ROOT_DIR . $folder . $file . self::TPL_FILE_EXTENSION;
      $this->exists();
      require_once $file;
    }

}
