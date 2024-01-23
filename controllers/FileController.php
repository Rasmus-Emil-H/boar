<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\File;

class FileController extends Controller {

	public function index() {
        if ($this->request->isGet()) return;

        try {
            if (empty($this->requestBody->files)) $this->response->setResponse(400, ['No files attached']);
            foreach ($this->requestBody->files as $newFile) {
                $file = new File($newFile);
                $file->moveFile();
            }
            $this->response->setResponse(201, ['File Uploaded']);
        } catch (\Throwable $error) {
            $this->response->setResponse(400, [$error->getMessage()]);
        }
	}

}