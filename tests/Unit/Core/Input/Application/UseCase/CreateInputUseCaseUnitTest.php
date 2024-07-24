<?php

use Core\Input\Application\DTO\CreateInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\CreateInputUseCase;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

test('should create new Input', function () {
    $dto = new CreateInputDTO(
        name: 'input name',
        description: 'input description',
    );
    $input = new Input($dto->name, $dto->description);
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('insert')
        ->times(1)
        ->andReturn($input);
    $useCase = new CreateInputUseCase(
        repository: $mockRepository
    );
    $response = $useCase->execute(dto: $dto);

    expect($response)->toBeInstanceOf(OutputInputDTO::class)
        ->and($response->name)->toBe($dto->name)
        ->and($response->description)->toBe($dto->description);
});
