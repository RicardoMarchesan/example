<?php

namespace Core\Input\Application\DTO;

class InputFindAllInputsDTO
{
    public function __construct(
        public string $filter = '',
        public string $orderBy = 'DESC',
    ) {}
}
