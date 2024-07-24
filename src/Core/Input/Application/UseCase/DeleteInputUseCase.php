<?php

namespace Core\Input\Application\UseCase;

use Core\Input\Application\DTO\DeleteOutputInputDTO;
use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

class DeleteInputUseCase
{
    public function __construct(private readonly InputRepositoryInterface $repository) {}

    public function execute(InputInputDTO $input): DeleteOutputInputDTO
    {
        $entity = $this->repository->findById($input->id);
        $response = $this->repository->delete($entity->id());

        return new DeleteOutputInputDTO(
            deleted: $response
        );
    }
}
