<?php

namespace Core\Input\Application\DTO;

class CreateInputDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $description
    ) {}
}
