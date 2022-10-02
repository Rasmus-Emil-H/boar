<?php

/*******************************
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

    /** 
     * @var string
    */

    public string $fileName;

    public function __construct(string $fileName) {
        $this->fileName = $fileName;
    }

    public function getUploadedFile(string $fileName) {
        return basename($_FILES[$this->fileName]["name"]) ?? throw new \Exception('File not found');
    }

    public function moveFile(): bool {
        if ( !$this->checkFileType() ) throw new \Exception(self::INVALID_EXTENSION);
        if ( !$this->checkFileName() ) throw new \Exception(self::INVALID_FILE_NAME);
        return move_uploaded_file(sys_get_temp_dir(), Application::UPLOAD_FOLDER);

    }

    protected function checkFileType(): bool {
        return in_array(filetype($this->getUploadedFile()), $this->allowedFileExtensions);
    }

    public function checkFileName (): bool{
        return preg_match("`^[-0-9A-Z_\.]+$`i", $this->fileName);
    }

    public function unlinkFile(): bool {
        return unlink($this->fileName);
    }

    public function getFile() {
        try {
            
        } catch (\Exception $e) {

        }
    }

}