<?php

namespace Core\Input\Application\DTO;

class OutputInputsDTO
{
    public function __construct(
        /**
         * @return array<OutInputDTO>
         */
        public array $items,
        public int $total,
        public int $last_page,
        public int $total_per_page,
        public int $current_page,
        public ?int $first_page,
        public ?int $next_page,
        public ?int $previous_page,
    ) {}
}
