<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\PushModel;

class PushController extends Controller {
    
    public function subscribe() {
        if ($this->request->isGet())
            $this->response->ok((new PushModel())->getPublicKey());

        (new PushModel())->truncate();

        $object = json_decode($this->requestBody->body->body);
        $push = new PushModel();
        $push->setData([
            'Endpoint' => $object->endpoint, 
            'ExpirationTime' => $object->expirationTime ?? 'ok', 
            'PubSubKeys' => json_encode($object->keys), 
            'UserID' => CoreFunctions::applicationUser()->key()
        ]);
        $push->save();
        
        $this->response->ok();
    }

    public function ping() {
        $this->denyPOSTRequest();

        if (!CoreFunctions::applicationUser()) $this->response->notFound();

        $sub = (new PushModel())->find('UserID', $this->requestBody->body->userID);
        $sub->run((array)$sub->getData(), 'qwd');

        $this->response->ok();
    }
    
}