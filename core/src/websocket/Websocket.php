<?php

namespace app\core\src\websocket;

class Websocket {

    private ServerConfig $serverConfig;
    private ClientManager $clientManager;
    private HandshakeHandler $handshakeHandler;
    private MessageHandler $messageHandler;

    public function __construct() {
        
        $this->serverConfig = new ServerConfig(
            '0.0.0.0', 12345, 
            'YOUR_CERT_PATH', 
            'YOUR_KEY_PATH'
        );

        Logger::checkPortUsage($this->serverConfig->getPort());

        $context = $this->serverConfig->getStreamContext();
        $server = stream_socket_server(
            'ssl://' . $this->serverConfig->getAddress() . ':' . $this->serverConfig->getPort(), 
            $errno, 
            $errstr, 
            STREAM_SERVER_BIND | STREAM_SERVER_LISTEN, 
            $context
        );

        if (!$server) die("Error: $errstr ($errno)");

        stream_set_blocking($server, false);
        Logger::yell("Server started at {$this->serverConfig->getAddress()}:{$this->serverConfig->getPort()}\n");

        $this->clientManager    = new ClientManager($server);
        $this->handshakeHandler = new HandshakeHandler();
        $this->messageHandler   = new MessageHandler();

        $this->main();
    }

    private function main() {
        while (true) {
            sleep(1);
            $readSockets = array_merge([$this->clientManager->getServer()], $this->clientManager->getClients());
            $writeSockets = null;
            $exceptSockets = null;

            if (stream_select($readSockets, $writeSockets, $exceptSockets, 0) > 0) {
                if (in_array($this->clientManager->getServer(), $readSockets)) {
                    $client = $this->clientManager->acceptClient();

                    if ($client && !$this->handshakeHandler->performHandshake($client))
                        $this->clientManager->removeClient($client);
                }

                foreach ($readSockets as $client) {
                    $data = fread($client, 5000);
                    $this->clientManager->removePassiveClient($client, $data);
                    $this->messageHandler->handleMessage($client, $data);
                }
            }

            $this->messageHandler->broadcastMessage($this->clientManager->getClients(), "Now: " . time());
        }
    }
}
