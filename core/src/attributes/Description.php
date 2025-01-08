<?php

namespace app\core\src\attributes;

#[\Attribute]
class Description {
    public function __construct(
        public string $summary,
        public string $author,
        public string $package
    ) {}
}