<?php

/**
 * Bootstrap Session 
 * AUTHOR: RE_WEB
 * @package app\core\Session
 */

namespace app\core;

class Session {

    protected const FLASH_ARRAY = 'FLASH_MESSAGES';

    public function __construct() {
        $this->checkFlashMessages();
    }

    public function checkFlashMessages() {
        $flashMessages = $this->getAllFlashMessages();
        foreach ($flashMessages as &$flashMessage) $flashMessage['remove'] = true;
        $_SESSION[self::FLASH_ARRAY] = $flashMessages;
    }

    public function setFlashMessage(string $key, string $message) {
        $_SESSION[self::FLASH_ARRAY][$key] = [
            'remove' => false,
            'value' => $message
        ];
    }

    public function getFlashMessage(string $key): string {
        return $_SESSION[self::FLASH_ARRAY][$key]['value'] ?? '';
    }

    public function getAllFlashMessages(): array {
        return $_SESSION[self::FLASH_ARRAY] ?? [];
    }

    public function set(string $key, string $value) {
        $_SESSION[$key] = $value;
    }

    public function get(string $key): mixed {
        return $_SESSION[$key] ?? '';
    }

    public function unset(string $key): void {
        unset($_SESSION[$key]);
    }

    public function getAll(): array {
        return $_SESSION;
    }

    public function nullAll(): void {
        foreach ($this->getAll() as &$value) $value = null;
    }

    public function __destruct() {
        $flashMessages = $this->getAllFlashMessages();
        foreach ($flashMessages as $key => &$flashMessage) if ($flashMessage['remove']) $this->unset($flashMessages[$key]);
        $_SESSION[self::FLASH_ARRAY] = $flashMessages;
    }

}