<?php

namespace Core\Input\Application\DTO;

class DeleteOutputInputDTO
{
    public function __construct(
        public readonly bool $deleted,
    ) {}
}
