<?php

namespace Core\Input\Application\DTO;

class EditInputDTO
{
    public function __construct(
        public string  $id,
        public string  $name,
        public ?string $description = null
    ) {}
}
