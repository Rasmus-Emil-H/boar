<?php

namespace app\core\src;

class WebApplicationFirewall {
    protected $inputData;

    public function __construct() {
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
        foreach ($this->inputData as $value)
            if (preg_match("/SELECT.*FROM|UNION.*SELECT|DROP.*TABLE|INSERT.*INTO|DELETE.*FROM/i", $value))
                $this->blockRequest("SQL Injection attempt detected");
    }

    protected function detectXSS() {
        foreach ($this->inputData as $value)
            if (preg_match("/<script|<img|onerror|javascript:/i", $value)) 
                $this->blockRequest("XSS attempt detected");
    }

    protected function blockRequest($reason) {
        die("Request blocked: " . $reason);
    }
}