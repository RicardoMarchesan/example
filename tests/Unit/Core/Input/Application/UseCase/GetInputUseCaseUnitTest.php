<?php

use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\GetInputUseCase;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use Core\SeedWork\Domain\ValueObjects\Uuid;

test('should return input', function () {
    $dto = new InputInputDTO(
        id: '123.123.3212'
    );

    $idEntityInput = Uuid::random();
    $mockEntityInput = Mockery::mock(Input::class, [
        'name_input', 'description_input', $idEntityInput,
    ]);
    $mockEntityInput->shouldReceive('id')->andReturn((string) $idEntityInput);

    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->with($dto->id)
        ->andReturn($mockEntityInput);
    $useCase = new GetInputUseCase(
        repository: $mockRepository
    );
    $response = $useCase->execute(input: $dto);
    expect($response)->toBeInstanceOf(OutputInputDTO::class)
        ->and($response->id)->toBe((string) $idEntityInput)
        ->and($response->id)->toBeString()
        ->and($response->name)->toBe('name_input')
        ->and($response->description)->toBe('description_input');
});

test('should throw not found exception', function () {
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->andThrows(new EntityNotFoundException('Input not found'));
    $useCase = new GetInputUseCase(
        repository: $mockRepository
    );

    $useCase->execute(
        input: new InputInputDTO(
            id: '123.123.3212'
        )
    );
})->throws(EntityNotFoundException::class);
