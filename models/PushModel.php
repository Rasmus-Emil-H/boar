<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\miscellaneous\Encrypt;
use \app\core\src\miscellaneous\PushManager;

final class PushModel extends Entity {

    public function getTableName(): string {
        return 'Push';
    }

    public function getKeyField(): string {
        return 'PushID';
    }

    public function run($info, $data): mixed {
        $push = new PushManager(Encrypt::decrypt($info['Endpoint']));
        $push->setUserPayLoad($data);
        return $push->sendNotification();
    }
    
    public function getPublicKey(): string {
        $push = new PushManager(null);
        return $push->getVapidPublicKey();
    } 

}
