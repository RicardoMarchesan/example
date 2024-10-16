<?php

namespace Core\SeedWork\Domain\Repositories;

use stdClass;

interface PaginationInterface
{
    /**
     * @return array<stdClass>
     */
    public function items(): array;

    public function total(): int;

    public function lastPage(): int;

    public function firstPage(): ?int;

    public function totalPerPage(): int;

    public function nextPage(): ?int;

    public function previousPage(): ?int;

    public function currentPage(): int;
}
