<?php

namespace Core\Input\Application\UseCase;

use Core\Input\Application\DTO\InputFindAllInputsDTO;
use Core\Input\Application\DTO\OutputFindAllInputsDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

readonly class FindAllInputsUseCase
{
    public function __construct(private InputRepositoryInterface $repository) {}

    public function execute(InputFindAllInputsDTO $input): OutputFindAllInputsDTO
    {
        $entities = $this->repository->findAll($input->filter, $input->orderBy);

        return new OutputFindAllInputsDTO(
            items: array_map(fn ($entity) => OutputInputDTO::fromEntity($entity), $entities),
            total: count($entities)
        );
    }
}
