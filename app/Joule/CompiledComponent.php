<?php

namespace App\Joule;

class CompiledComponent
{
    public function __construct(
        public string $name,
        public string $class,
    ) {
    }
}
