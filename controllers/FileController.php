<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\factories\EntityFactory;
use \app\core\src\File;
use \app\core\src\gate\Gate;
use \app\models\FileModel;

class FileController extends Controller {

    /**
     * Default method for receiving files from the client and handling them accordingly based on some entity
     */

	public function index() {
        $this->validateFileRequest();

        try {
            $request = $this->requestBody;

            $request->entity = new EntityFactory([
                'handler' => $request->body->entityType, 
                'key' => $request->body->entityID
            ]);

            $request->customFileType = $request->body->type ?? 'defaultType';

            foreach ($request->files as $newFile) {
                $request->file = new File($newFile);
                $request->destination = $request->file->moveFile();

                $cFile = new FileModel();
                $files[] = $cFile->dispatchHTTPMethod('attachFile', $request);
            }

            $files['message'] = hs('Files added');
            
            $this->response->ok($files);
        } catch (\Exception $error) {
            debug($error);
            $this->response->setResponse(400, [File::NO_FILES_ATTACHED]);
        }
	}

    public function view() {
        $this->denyPOSTRequest();

        $cFile = $this->returnValidEntityIfExists();
        $cFile->requireExistence();

        return $this->response->customResponse('file', base64_encode(file_get_contents($cFile->get('Path'))));
    }

    private function validateFileRequest(): void {
        $this->denyGETRequest();
        
        if (empty($this->requestBody->files)) $this->response->setResponse(400, [File::NO_FILES_ATTACHED]);
    }

}