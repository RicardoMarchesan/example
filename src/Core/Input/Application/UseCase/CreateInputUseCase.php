<?php

namespace Core\Input\Application\UseCase;

use Core\Input\Application\DTO\CreateInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityValidationException;

class CreateInputUseCase
{
    public function __construct(private readonly InputRepositoryInterface $repository) {}

    /**
     * @throws EntityValidationException
     */
    public function execute(CreateInputDTO $dto): OutputInputDTO
    {
        $input = new Input($dto->name, $dto->description);
        $entity = $this->repository->insert($input);

        return OutputInputDTO::fromEntity($entity);
    }
}
