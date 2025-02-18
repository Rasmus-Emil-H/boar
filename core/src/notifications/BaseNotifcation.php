<?php

namespace app\core\src\notifications;

use app\core\src\http\Curl as HttpCurl;

abstract class BaseNotifications {

    public function __construct(
        protected array|object $notificationData,
        protected HttpCurl $curl = new HttpCurl()
    ) {}

    public function getCurl(): HttpCurl {
        return $this->curl;
    }

    public function getNotifcationData(): object {
        return (object)$this->notificationData;
    }

}