<?php

namespace app\core\src;

use \app\core\src\exceptions\NotFoundException;
use \app\core\src\miscellaneous\CoreFunctions;

final class File {

    protected array $allowedFileExtensions = ['jpg', 'jpeg', 'webp', 'png'];

    public const INVALID_EXTENSION  = 'Invalid file extension';
    public const INVALID_FILE_NAME  = 'Invalid file name';
    public const INVALID_FILE_SIZE  = 'File is to big';
    public const TPL_FILE_EXTENSION = '.tpl.php';
    public const VIEWS_FOLDER       = '/views/';

    protected const FILE_NOT_FOUND     = 'File not found';
    protected const MAXIMUM_FILE_SIZE  = 10000000;

    public function __construct(
        protected $file,
        protected $uploadFolder = null
    ) {
        $this->uploadFolder ??= dirname(__DIR__, 2).'/uploads/';
    }

    public function moveFile(): bool {
        if (!$this->checkFileType()) throw new \Exception(self::INVALID_EXTENSION);
        if (!$this->validateFileName()) throw new \Exception(self::INVALID_FILE_NAME);
        if (!$this->validateSize()) throw new \Exception(self::INVALID_FILE_SIZE);
        $destination = $this->uploadFolder.(strtotime('now').'-'.$this->file['name']);
        return move_uploaded_file($this->file['tmp_name'], $destination);
    }

    public function validateSize(): bool {
        return $this->file['size'] < self::MAXIMUM_FILE_SIZE;
    }

    protected function checkFileType(): bool {
        $fileType = preg_replace('~.*' . preg_quote('/', '~') . '~', '', $this->file['type']);
        return in_array($fileType, $this->allowedFileExtensions);
    }

    public function validateFileName(): bool {
        $fileName = preg_replace('~\..*~', '', $this->file['name']);
        return preg_match('/[a-zA-Z0-9]/', $fileName);  
    }

    public function unlinkFile(): bool {
        return unlink($this->uploadFolder . $this->file['name']);
    }

    public function getFile() {
        if (!file_exists($this->uploadFolder . $this->file['name'])) throw new \Exception(self::FILE_NOT_FOUND);
    }

    public function exists() {
      if (!file_exists($this->file)) throw new NotFoundException();
    }

    public function requireApplicationFile(string $folder, string $file, array $params = []): void {
      $file = CoreFunctions::app()::$ROOT_DIR . $folder . $file . self::TPL_FILE_EXTENSION;
      $this->exists();
      require_once $file;
    }

}
