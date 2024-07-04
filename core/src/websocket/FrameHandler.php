<?php

namespace app\core\src\websocket;

class FrameHandler {

    private const DEFAULT_OFFSET = 7;
    private const NEXT_TWO_BYTES_IS_PAYLOAD_LENGTH   = 126;
    private const NEXT_EIGHT_BYTES_IS_PAYLOAD_LENGTH = 127;

    public function __construct() {

    }

    /**
     * Credits:
     * https://www.openmymind.net/WebSocket-Framing-Masking-Fragmentation-and-More/
     */

    public function decodeFrame($data) {
        $bytes = unpack('C*', $data);

        // AND to get the payload length
        $payloadLength = $bytes[2] & 127;

        switch ($payloadLength) {
            case self::NEXT_TWO_BYTES_IS_PAYLOAD_LENGTH:
                $mask = array_slice($bytes, 5, 4);
                $payloadOffset = self::DEFAULT_OFFSET + 2;
                break;
            case self::NEXT_EIGHT_BYTES_IS_PAYLOAD_LENGTH:
                $mask = array_slice($bytes, 11, 4);
                $payloadOffset = self::DEFAULT_OFFSET + 8;
            default:
                $mask = array_slice($bytes, 3, 4);
                $payloadOffset = self::DEFAULT_OFFSET;
                break;
        }

        $payload = array_slice($bytes, $payloadOffset);

        // Process payload with the mask
        for ($i = 0; $i < count($payload); $i++)
            $payload[$i] ^= $mask[$i % 4];
    
        return implode(array_map('chr', $payload));
    }

}