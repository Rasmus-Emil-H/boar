<?php

namespace app\core\src\database\adapters;

use PDO;

class PDOBridge extends Adapter {

    public function __construct(
        protected PDO $pdo
    ) {}

    public function getDriverName(): string {
        return $this->pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
    }

    public function doConnect() {

    }

}