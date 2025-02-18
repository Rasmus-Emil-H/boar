<?php

namespace app\core\src\notifications\implementations;

use \app\core\src\contracts\Notification;

use \app\core\src\notifications\BaseNotifications;

final class Email extends BaseNotifications implements Notification {

    private function data(): array {
        $info = $this->getNotifcationData();

        return [
			"return_path" => '',
			"recipients" => [
                [
                    'address' => $info->receiver
                ]
            ],
			"options" => [
                'transactional' => true
            ],
			"content" => [
				'from' => [
					'email' => '',
					'name' => env('appName'),
                ],
				'subject' => $info->subject,
				'text' => trim(strip_tags($info->message)),
				'html' => $info->message,
            ],
        ];
    }

    public function send() {
        
    }

}