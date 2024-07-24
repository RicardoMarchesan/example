<?php

namespace Core\Input\Application\UseCase;

use Core\Input\Application\DTO\EditInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

class EditInputUseCase
{
    public function __construct(
        private InputRepositoryInterface $repository
    ) {}

    public function execute(EditInputDTO $input): OutputInputDTO
    {
        $stored_input = $this->repository->findById($input->id);
        $stored_input->update(
            name: $input->name,
            description: $input->description
        );

        $entityUpdated = $this->repository->update($stored_input);
        return OutputInputDTO::fromEntity($entityUpdated);
    }
}
