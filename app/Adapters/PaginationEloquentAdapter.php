<?php

namespace App\Adapters;

use Core\SeedWork\Domain\Repositories\PaginationInterface;
use Illuminate\Pagination\LengthAwarePaginator;
use stdClass;

class PaginationEloquentAdapter extends LengthAwarePaginator implements PaginationInterface
{
    public function __construct(private readonly LengthAwarePaginator $paginator) {}

    /**
     * @return array<stdClass>
     */
    public function items(): array
    {
        return $this->convertArrayToStdClass($this->paginator->items());
    }

    public function total(): int
    {
        return (int) $this->paginator->total();
    }

    public function lastPage(): int
    {
        return (int) $this->paginator->lastPage();
    }

    public function firstPage(): ?int
    {
        return $this->paginator->firstItem() === null ? null : 1;
    }

    public function totalPerPage(): int
    {
        return (int) $this->paginator->perPage();
    }

    public function nextPage(): ?int
    {
        if (! $this->paginator->hasMorePages()) {
            return null;
        }

        return (int) $this->paginator->currentPage() + 1;
    }

    public function previousPage(): ?int
    {
        if ($this->paginator->currentPage() === 1) {
            return null;
        }

        return (int) $this->paginator->currentPage() - 1;
    }

    public function currentPage(): int
    {
        return $this->paginator->currentPage();
    }

    /**
     * @return stdClass[]
     */
    private function convertArrayToStdClass(array $items): array
    {
        return array_map(fn ($item) => (object) $item->toArray(), $items);
    }
}
