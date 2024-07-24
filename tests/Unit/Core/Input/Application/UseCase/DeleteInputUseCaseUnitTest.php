<?php

use Core\Input\Application\DTO\DeleteOutputInputDTO;
use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Application\UseCase\DeleteInputUseCase;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use Core\SeedWork\Domain\ValueObjects\Uuid;

test('should delete a input', function () {
    $uuid = Uuid::random();
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('delete')
        ->times(1)
        ->with((string) $uuid)
        ->andReturn(true);
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->andReturn(new Input('name', 'description', $uuid));
    $useCase = new DeleteInputUseCase(
        repository: $mockRepository
    );
    $response = $useCase->execute(
        input: new InputInputDTO(
            id: $uuid
        )
    );
    expect($response)->toBeInstanceOf(DeleteOutputInputDTO::class)
        ->and($response->deleted)->toBeTrue();
});

test('should delete a input - return false', function () {
    $uuid = Uuid::random();
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('delete')
        ->times(1)
        ->with((string) $uuid)
        ->andReturn(false);
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->andReturn(new Input('name', 'description', $uuid));
    $useCase = new DeleteInputUseCase(
        repository: $mockRepository
    );
    $response = $useCase->execute(
        input: new InputInputDTO(
            id: $uuid
        )
    );
    expect($response)->toBeInstanceOf(DeleteOutputInputDTO::class)
        ->and($response->deleted)->toBeFalse();
});

test('should throw an exception when input not found', function () {
    $uuid = Uuid::random();
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('findById')
        ->times(1)
        ->andThrow(new EntityNotFoundException('Input not found'));
    $useCase = new DeleteInputUseCase(
        repository: $mockRepository
    );
    $useCase->execute(
        input: new InputInputDTO(
            id: $uuid
        )
    );
})->throws(EntityNotFoundException::class);
