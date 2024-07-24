<?php

namespace Core\Input\Application\UseCase;

use Core\Input\Application\DTO\InputInputsDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\DTO\OutputInputsDTO;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

class GetInputsUseCase
{
    public function __construct(private InputRepositoryInterface $repository) {}

    public function execute(InputInputsDTO $input): OutputInputsDTO
    {
        $response = $this->repository->paginate(
            filter: $input->filter,
            orderBy: $input->orderBy,
            page: $input->page,
            totalPerPage: $input->totalPerPage
        );

        return new OutputInputsDTO(
            items: $this->convertStdClassToDTO($response->items()),
            total: $response->total(),
            last_page: $response->lastPage(),
            total_per_page: $response->totalPerPage(),
            current_page: $response->currentPage(),
            first_page: $response->firstPage(),
            next_page: $response->nextPage(),
            previous_page: $response->previousPage(),
        );
    }

    /**
     * @return array<OutputInputDTO>
     * @throws \Exception
     */
    private function convertStdClassToDTO(array $items = []): array
    {
        return array_map(function ($stdClass) {
            return new OutputInputDTO(
                id: $stdClass->id,
                name: $stdClass->name,
                description: $stdClass->description,
                created_at: isset($stdClass->created_at) && $stdClass->created_at instanceof \DateTime ? $stdClass->created_at->format('Y-m-d H:i:s') : null,
                updated_at: isset($stdClass->updated_at) && $stdClass->updated_at instanceof \DateTime ? $stdClass->updated_at->format('Y-m-d H:i:s') : null,
                deleted_at: isset($stdClass->deleted_at) && $stdClass->deleted_at instanceof \DateTime ? $stdClass->deleted_at->format('Y-m-d H:i:s') : null
            );
        }, $items);
    }
}
