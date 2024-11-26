<?php

namespace app\controllers;

use \app\core\src\Controller;
use \app\core\src\miscellaneous\CoreFunctions;
use \app\models\PushModel;
use \app\core\src\miscellaneous\Encrypt;

class PushController extends Controller {
    
    public function subscribe() {
        $user = CoreFunctions::applicationUser();
        
        if (is_null($user)) $this->response->notAllowed();

        if ($this->request->isGet())
            $this->response->ok((new PushModel())->getPublicKey());
        
        $object = json_decode($this->requestBody->body->body);

        $currentSub = (new PushModel())->find('UserID', $user->key());

        if (Encrypt::decrypt($currentSub->get('Endpoint')) === $object->endpoint) $this->response->ok();

        (new PushModel())->deleteWhere(['UserID' => $user->key()]);

        (new PushModel())->setAndSave([
            'Endpoint' => Encrypt::encrypt($object->endpoint),
            'ExpirationTime' => $object->expirationTime ?? 'ok', 
            'PubSubKeys' => Encrypt::encrypt(json_encode($object->keys)),
            'UserID' => $user->key()
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