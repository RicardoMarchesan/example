<?php

namespace Core\Input\Application\UseCase;

use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

readonly class GetInputUseCase
{
    public function __construct(private InputRepositoryInterface $repository) {}

    public function execute(InputInputDTO $input, bool $withTrashed = false): OutputInputDTO
    {
        $input = $this->repository->findById($input->id, $withTrashed);

        return OutputInputDTO::fromEntity($input);
    }
}
