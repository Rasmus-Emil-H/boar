<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\factories\EntityFactory;
use \app\core\src\File;
use \app\models\FileModel;

class FileController extends Controller {

    public function validateFileRequest(): void {
        if ($this->request->isGet()) $this->response->methodNotAllowed();
        if (empty($this->requestBody->files)) $this->response->setResponse(400, [File::NO_FILES_ATTACHED]);
    }

	public function index() {
        $this->validateFileRequest();

        try {
            $request = $this->requestBody;

            $entity = new EntityFactory([
                'handler' => $request->body->entityType, 
                'key' => $request->body->entityID
            ]);

            $cEntity = $entity->create();

            $customFileType = $request->body->type ?? 'defaultType';

            foreach ($request->files as $newFile) {
                $file = new File($newFile);
                $destination = $file->moveFile();

                $cFile = new FileModel();
                $cFile->setData([
                    'Name' => $file->getName(),
                    'Path' => $destination,
                    'Hash' => hash_file('sha256', $destination),
                    'Type' => $customFileType
                ]);

                $cFile
                    ->save()
                    ->createPivot(['EntityType' => $cEntity->getTableName(), 'EntityID' => $cEntity->key(), 'FileID' => $cFile->key()]);

                $uploadedFiles[$customFileType] = $file->getName();
            }
            
            $this->response->setResponse(201, $uploadedFiles);
        } catch (\Exception $error) {
            $this->response->setResponse(400, [$error->getMessage()]);
        }
	}

}