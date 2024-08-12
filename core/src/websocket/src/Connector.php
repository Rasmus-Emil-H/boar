<?php

namespace app\core\src\websocket\src;

class Connector {

    private const WAIT_FOR_MESSAGE_KEY = 'wait';

    public static function sendToServer(mixed $message = Constants::DEFAULT_CLIENT_MESSAGE) {
        $client = self::tryConnect();
        if (!$client) return;

        self::sendWebSocketMessage($client, $message);

        if (str_contains($message, self::WAIT_FOR_MESSAGE_KEY)) return self::waitForMessage($client);

        fclose($client);
    }

    private static function waitForMessage($client) {
        return self::waitForResponse($client);
    }

    /**
     * In case you want something back
     */
    
    private static function waitForResponse($client) {
        $startTime = time();
        $timeout = 10;
        $buffer = '';

        while (time() - $startTime < $timeout) {
            $data = fread($client, 1024);

            if ($data) {
                $buffer .= $data;
                break;
            }

            usleep(500000);
        }

        return $buffer;
    }

    private static function tryConnect(): mixed {

        $websocketConfigs = Constants::getConfigs();

        $serverConfig = new ServerConfig(
            address: $websocketConfigs->address, 
            port: $websocketConfigs->port, 
            certFile: $websocketConfigs->paths->cert,
            keyFile: $websocketConfigs->paths->key
        );

        $context = $serverConfig->getBackendClientStreamContext();
        $address = $serverConfig->getAddress() . ':' . $serverConfig->getPort();

        $client = stream_socket_client('ssl://' . $address, $errno, $errstr, null, STREAM_CLIENT_CONNECT, $context);

        if (!$client) {
            app()->getResponse()->ok("Failed to connect: $errstr ($errno)\n");
            return false;
        }

        if (!self::performHandshake($client)) {
            fclose($client);
            return false;
        }

        return $client;
    }

    private static function performHandshake($client) {
        $websocketConfigs = Constants::getConfigs();

        $key = base64_encode(openssl_random_pseudo_bytes(16));

        $handshaker = new HandshakeHandler();
        $headers = $handshaker->prepareBackendClientHeaders($key, $websocketConfigs);

        fwrite($client, $headers);
        $response = fread($client, 5000);

        if (preg_match(Constants::ACK_RESPONSE, $response, $matches)) return true;
        
        Logger::yell(Constants::INVALID_HANDSHAKE_RESPONSE . $response);
        return false;
    }

    private static function sendWebSocketMessage($client, $message) {
        fwrite($client, FrameHandler::encodeWebSocketFrame($message));
    }

    private static function getWebsocketMessage($client) {
        return fread($client, 5000);
    }

}
