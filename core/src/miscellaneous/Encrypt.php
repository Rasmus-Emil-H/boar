<?php

/**
|----------------------------------------------------------------------------
| Default encryption
|----------------------------------------------------------------------------
|
| @author RE_WEB
| @package core
|
*/

namespace app\core\src\miscellaneous;

class Encrypt {

    private static function generateKeys(): array {
        return [
            'first' => base64_encode(openssl_random_pseudo_bytes(32)),
            'second' => base64_encode(openssl_random_pseudo_bytes(64))
        ];
    }

    private static function getConfig(): object {
        return app()->getConfig()->get('encryption')->openssl;
    }

    public static function encrypt(mixed $data): string {
        $config = self::getConfig();

        $ivLength = openssl_cipher_iv_length($config->method);
        $iv = openssl_random_pseudo_bytes($ivLength);

        $first = openssl_encrypt($data, $config->method, base64_decode($config->firstKey), OPENSSL_RAW_DATA, $iv);
        $second = hash_hmac($config->hashMacAlgo, $first, base64_decode($config->secondKey), TRUE);

        return base64_encode($iv . $second . $first);
    }

    public static function decrypt(mixed $data): bool|string {
        $config = self::getConfig();

        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length($config->method);
        $iv = substr($data, 0, $ivLength);

        $second = substr($data, $ivLength, 64);
        $first   = substr($data, $ivLength + 64);

        $knownString = openssl_decrypt($first, $config->method, $first, OPENSSL_RAW_DATA, $iv);
        $userString = hash_hmac($config->hashMacAlgo, $first, $second, TRUE);

        if (hash_equals($knownString, $userString)) return $data;

        return false;
    }

}