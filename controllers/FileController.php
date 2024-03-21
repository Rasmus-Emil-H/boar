<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\factories\EntityFactory;
use \app\core\src\File;
use \app\core\src\gate\Gate;
use \app\models\FileModel;

class FileController extends Controller {

    public function validateFileRequest(): void {
        $this->denyGETRequest();
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

                $path = file_get_contents($cFile->get('Path'));
                $b64 = 'data:image/jpeg;base64,' . base64_encode($path);

                $files[] = ['b64' => $b64, 'id' => $cFile->key()];
            }

            $files['message'] = hs('Files added');
            
            $this->response->ok($files);
        } catch (\Exception $error) {
            $this->response->setResponse(400, [$error->getMessage()]);
        }
	}

    public function delete() {

        $this->denyGETRequest();

        $cFile = new FileModel($this->requestBody->body->EntityID);
        // if (!Gate::canEditFile($cFile)) $this->response->notAllowed();
        if (!$cFile->exists()) $this->response->dataConflict(hs(File::FILE_NOT_FOUND));
        
        $cFile->delete();

        $this->response->ok(hs('File deleted'));
    }

}