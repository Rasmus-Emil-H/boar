<?php

/*******************************
 * Bootstrap Session 
 * AUTHOR: RE_WEB
 * @package app\core\Session
*/

namespace app\core;

class Session {

    protected const FLASH_ARRAY = 'FLASH_MESSAGES';

    public function __construct() {
        session_start();
        $this->checkFlashMessages();
    }

    public function checkFlashMessages() {
        $flashMessages = $this->getAllFlashMessages();
        foreach ($flashMessages as &$flashMessage) 
            $flashMessage['remove'] = true;
        $_SESSION[self::FLASH_ARRAY] = $flashMessages;
    }

    public function setFlashMessage(string $key, string $message) {
        $_SESSION[self::FLASH_ARRAY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlashMessage(string $key) {
        return $_SESSION[self::FLASH_ARRAY][$key]['value'] ?? '';
    }

    public function getAllFlashMessages() {
        return $_SESSION[self::FLASH_ARRAY] ?? [];
    }

    public function set(string $key, string $value) {
        $_SESSION[$key] = $value;
    }

    public function get(string $key) {
        return $_SESSION[$key] ?? '';
    }

    public function removeSessionProperty($key): void {
        unset($_SESSION[$key]);
    }

    public function __destruct() {
        $flashMessages = $this->getAllFlashMessages();
        foreach ($flashMessages as $key => &$flashMessage) if ($flashMessage['remove']) unset($flashMessages[$key]);
        $_SESSION[self::FLASH_ARRAY] = $flashMessages;
    }

}