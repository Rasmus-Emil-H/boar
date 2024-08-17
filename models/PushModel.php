<?php

namespace app\models;

use \app\core\src\database\Entity;
use \app\core\src\miscellaneous\PushManager;

final class PushModel extends Entity {

    public function getTableName(): string {
        return 'Push';
    }

    public function getKeyField(): string {
        return 'PushID';
    }

    public function run($info, $data) {
        $push = new PushManager($info['Endpoint']);
        $push->setUserPayLoad(['title' => 'o', 'message' => 'k']);
        $push->sendNotification();
    }
    
    public function getPublicKey() {
        $push = new PushManager(null);
        return $push->getVapidPublicKey();
    } 

}