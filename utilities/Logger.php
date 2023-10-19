<?php

namespace app\utilities;

class Logger {

    public function log($data): void {
        $seperator = ' --------------------- ';
        $message = ($seperator . date('d-m-Y H:i:s') . ' ERROR: ' . $data->getMessage() . ' TRACE ' . json_encode($data->getTrace(), JSON_PRETTY_PRINT) . $seperator);
        $file = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'debug.log';
        file_put_contents($file, PHP_EOL . $message . PHP_EOL, FILE_APPEND | LOCK_EX);
    }

}