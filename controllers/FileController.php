<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\File;

use \app\core\src\factories\EntityFactory;

use \app\core\src\gate\Gate;

use \app\models\FileModel;

class FileController extends Controller {

    /**
     * Default method for receiving files from the client and handling them accordingly based on some entity
     */

	public function index() {
        $this->validateFileRequest();

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
	}

    public function view(object $request, FileModel $cFile) {
        $this->denyPOSTRequest();

        $cFile->requireExistence();
        
        $file = new File($cFile->get('Path'));

        return $this->response->customResponse('file', 'data:image/' . $file->getFileType() . ';base64, ' . base64_encode(file_get_contents($cFile->get('Path'))));
    }

    private function validateFileRequest(): void {
        $this->denyGETRequest();

        if (empty($this->requestBody->files)) {
            $this->response->setResponse(400, [File::NO_FILES_ATTACHED]);
        }

        $file = $this->requestBody->files['image'];

        if (!isset($file['entityType']) || !isset($file['entityID'])) {
            $this->response->setResponse(400,  ['Error']);
        }
    }

    public function delete(object $body, FileModel $file) {
        $file->delete();
        $this->response->ok();
    }

}