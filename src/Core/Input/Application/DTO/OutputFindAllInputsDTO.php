<?php

namespace Core\Input\Application\DTO;

class OutputFindAllInputsDTO
{
    public function __construct(
        /**
         * @return array<OutputInputDTO>
         */
        public array $items,
        public int $total,
    ) {}
}
