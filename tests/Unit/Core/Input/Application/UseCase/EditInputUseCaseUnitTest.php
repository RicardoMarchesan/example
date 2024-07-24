<?php

use Core\Input\Application\DTO\EditInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\EditInputUseCase;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use Core\SeedWork\Domain\ValueObjects\Uuid;

test('should edit input', function () {
    $uuid = Uuid::random();
    $dto = new EditInputDTO(
        id: (string) $uuid,
        name: 'name update',
        description: 'description update',
    );
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('update')
        ->times(1)
        ->andReturn(new Input($dto->name, $dto->description));
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->andReturn(new Input('name', 'description', $uuid));
    $useCase = new EditInputUseCase(
        repository: $mockRepository,
    );
    $response = $useCase->execute(
        input: $dto
    );

    expect($response)->toBeInstanceOf(OutputInputDTO::class)
        ->and($response->name)->toBe($dto->name)
        ->and($response->description)->toBe($dto->description);
});

test('should throw not found exception', function () {
    $uuid = Uuid::random();
    $dto = new EditInputDTO(
        id: (string) $uuid,
        name: 'name update',
        description: 'description update',
    );
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->andThrows(new EntityNotFoundException('Input not found'));
    $useCase = new EditInputUseCase(
        repository: $mockRepository
    );
    $useCase->execute(input: $dto);
})->throws(EntityNotFoundException::class);
