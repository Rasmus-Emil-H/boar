<?php

namespace app\core\src;

class WebApplicationFirewall {
    protected $inputData;

    protected const SQL_MESSAGE = 'SQL Injection attempt detected';
    protected const XSS_MESSAGE = 'XSS attempt detected';
    protected const DIE_MESSAGE = 'Request blocked: ';

    public function __construct(
        protected Request $request
    ) {
        $this->inputData = $_REQUEST;
        $this->sanitizeInput();
        $this->detectSQLInjection();
        $this->detectXSS();
    }

    protected function sanitizeInput() {
        foreach ($this->inputData as $key => $value)
            $this->inputData[$key] = htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    protected function detectSQLInjection() {
        foreach ($this->inputData as $value) {
            if (
                preg_match("/SELECT.*FROM|UNION.*SELECT|DROP.*TABLE|INSERT.*INTO|DELETE.*FROM|ALTER.*TABLE/i", $value) ||
                preg_match("/\b(AND|OR)\b.+\b(HAVING|FROM|JOIN|INTO|WHERE)\b/i", $value)
            ) $this->blockRequest(self::SQL_MESSAGE);
        }
    }

    protected function detectXSS() {
        foreach ($this->inputData as $value) {
            if (
                preg_match("/<script|<img|onerror|javascript:|document.cookie|eval\(|<iframe/i", $value) ||
                preg_match("/\b(alert|confirm|prompt)\(/i", $value)
            ) $this->blockRequest(self::XSS_MESSAGE);
        }
    }

    protected function blockRequest($reason) {
        die(self::DIE_MESSAGE . $reason);
    }
}