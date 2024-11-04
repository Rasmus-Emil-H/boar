<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\PushModel;
use \app\core\src\miscellaneous\Encrypt;

class PushController extends Controller {
    
    public function subscribe() {
        if ($this->request->isGet())
            $this->response->ok((new PushModel())->getPublicKey());

        (new PushModel())->deleteWhere(['UserID' => CoreFunctions::applicationUser()?->key()]);

        $object = json_decode($this->requestBody->body->body);
        
        (new PushModel())->setAndSave([
            'Endpoint' => Encrypt::encrypt($object->endpoint),
            'ExpirationTime' => $object->expirationTime ?? 'ok', 
            'PubSubKeys' => Encrypt::encrypt(json_encode($object->keys)),
            'UserID' => CoreFunctions::applicationUser()?->key()
        ]);
        
        $this->response->ok();
    }

    public function ping() {
        $this->denyPOSTRequest();

        $cPush = (new PushModel())->find('UserID', $this->requestBody->body->userID);

        if (!$cPush->exists())
            $this->response->notFound(ths('User not found'));

        $cPush->run((array)$cPush->getData(), $this->requestBody->body->message);

        $this->response->ok(ths('User pinged'));
    }
    
}