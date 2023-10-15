<?php

/*******************************
 * Bootstrap CsrfToken
 * Hardware based validation
 * AUTHOR: RE_WEB
 * @package app\core\CsrfToken
*/

namespace app\core\tokens;

use app\core\Application;

class CsrfToken {

    private $formTokenLabel = 'eg-csrf-token-label';
    private $sessionTokenLabel = 'EG_CSRF_TOKEN_SESS_IDX';
    private $post = [];
    private $session = [];
    private $server = [];
    private $excludeUrl = [];
    private $hashAlgo = 'sha256';
    private $hmac_ip = true;

    public function __construct($excludeUrl = null) {
        if (!is_null($excludeUrl)) $this->excludeUrl = $excludeUrl;
        $this->post = app()->request->getBody();
        $this->server = &$_SERVER;
        $this->session = app()->session;
    }

    public function setToken(): void {
        $this->session->set('csrf', $this->generateRandom());
    }

    public function getToken(): string {
        if (!$this->session->get('csrf')) $this->setToken();
        $token = $this->hmac_ip !== false ? $this->hMacWithIp($this->session->get($this->sessionTokenLabel)) : $this->session->get($this->sessionTokenLabel);
        return $token;
    }

    protected function generateRandom(): string {
        return bin2hex(openssl_random_pseudo_bytes(32));
    }

    /*
     * @return string
    */

    public function insertHiddenToken(): string {
        return "<input type=\"hidden\"" . " name=\"" . $this->xssafe($this->formTokenLabel) . "\"" . " value=\"" . $this->xssafe($this->getToken()) . "\"" . " />";
    }

    /*
     * @return string
    */

    public function xssafe($data, $encoding = 'UTF-8'): string {
        return htmlspecialchars($data, ENT_QUOTES | ENT_HTML401, $encoding);
    }

    /*
     * @return string
    */

    private function hMacWithIp(string $token): string {
        $hashHmac = hash_hmac($this->hashAlgo, app()->config->get('tokens')->csrf->hMacData, $token);
        return $hashHmac;
    }

    /*
     * @return string
    */

    private function getCurrentRequestUrl(): string {
        $currentUrl = 'https://' . $this->server['HTTP_HOST'] . $this->server['REQUEST_URI'];
        return $currentUrl;
    }

    /*
     * @return bool
    */

    public function validate(): bool {
        if (!in_array($this->getCurrentRequestUrl(), $this->excludeUrl)) {
            if (!empty($this->post)) {
                if (!$this->validateRequest()) return false;
                return true;
            }
        }
    }

    /*
     * Validate current request
     * @return bool
    */

    public function isValidRequest(): bool {
        $isValid = false;
        $currentUrl = $this->getCurrentRequestUrl();
        if (!in_array($currentUrl, $this->excludeUrl))
            if (!empty($this->post))
                $isValid = $this->validateRequest();
        return $isValid;
    }

    /*
     * Validate request
     * @return bool
    */
    
    public function validateRequest(): bool {
        if ($this->session->get($this->sessionTokenLabel)) return false;
        if (!empty($this->post[$this->formTokenLabel])) $token = $this->post[$this->formTokenLabel];
        else return false;
        $expected = $this->hmac_ip ? $this->hMacWithIp($this->session->get($this->sessionTokenLabel)) : $this->session->get($this->sessionTokenLabel);
        return hash_equals($token, $expected);
    }

    /**
     * removes the token from the session
     * @return void
    */

    public function unsetToken() {
        if ($this->session->get($this->sessionTokenLabel)) $this->session->unset($this->sessionTokenLabel);
    }

}