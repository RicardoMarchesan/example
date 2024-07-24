<?php

namespace Core\Input\Domain\Repositories;

use Core\Input\Domain\Input;
use Core\SeedWork\Domain\Repositories\PaginationInterface;

interface InputRepositoryInterface
{
    public function insert(Input $input): Input;

    public function findById(string $id, bool $withTrashed = false): Input;

    /**
     * @return Input[]
     */
    public function findAll(string $filter = '', string $orderBy = 'DESC'): array;

    public function paginate(string $filter = '', string $orderBy = 'DESC', int $page = 1, int $totalPerPage = 15): PaginationInterface;

    public function update(Input $input): ?Input;

    public function delete(string $id): bool;
}
