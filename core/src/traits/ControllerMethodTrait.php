<?php

namespace app\core\src\traits;

use \app\models\FileModel;
use \app\core\src\database\Entity;
use \app\core\src\database\table\Table;
use \app\core\src\File;

trait ControllerMethodTrait {

    public function moveRequestFiles(Entity $entity, string $type = ''): array {
        $files = [];
        foreach ($this->requestBody->files as $newFile) {
            app()->addSystemEvent([$newFile]);
            $file = new File($newFile);
            if (empty($file->getName())) continue;
            if (!isset($this->requestBody->body->imageType)) throw new \app\core\src\exceptions\NotFoundException('No image type found!');
            $destination = $file->moveFile();

            $cFile = new FileModel();
            $cFile->setData([
                'Name' => $file->getName(),
                'Path' => $destination,
                'Hash' => hash_file('sha256', $destination),
                'Type' => $this->requestBody->body->imageType
            ]);

            $cFile->save();
            $cFile->createPivot([
				Table::ENTITY_TYPE_COLUMN => $entity->getTableName(), Table::ENTITY_ID_COLUMN => $entity->key(), $cFile->getKeyField() => $cFile->key()
			]);

            $files[] = $cFile->key();
        }
        return $files;
    }

    public function denyGETRequest() {
        if ($this->request->isGet()) 
            $this->response->methodNotAllowed();
    }

    public function denyPOSTRequest() {
        if ($this->request->isPost()) 
            $this->response->methodNotAllowed(); 
    }

    public function checkAction() {
        if (isset($this->requestBody->body->action)) return;
        throw new \app\core\src\exceptions\NotFoundException('Action was not found');
    }

    public function determineClientResponseMethod(mixed $dispatchedHTTPMethodResult): string {
        return is_string($dispatchedHTTPMethodResult) ? (is_int(strpos($dispatchedHTTPMethodResult ?? '', 'Errors')) ? 'dataConflict' : 'ok') : 'ok';
    }

    public function edit() {
        $this->denyGETRequest();

        $cEntity = $this->returnValidEntityIfExists();

        $request = $this->requestBody->body;
        $response = $cEntity->dispatchHTTPMethod($request->action, $request);

        $this->response->{$this->determineClientResponseMethod(dispatchedHTTPMethodResult: $response)}($response ?? '');
    }

    public function view() {
        $this->denyPOSTRequest();

        $cEntity = $this->returnValidEntityIfExists();

        $request = $this->requestBody->body;
        $response = $cEntity->dispatchHTTPMethod($request->action, $request);

        $this->response->{$this->determineClientResponseMethod(dispatchedHTTPMethodResult: $response)}($response ?? '');
    }

}