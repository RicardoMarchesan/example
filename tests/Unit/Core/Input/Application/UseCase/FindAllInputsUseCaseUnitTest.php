<?php

use Core\Input\Application\DTO\InputFindAllInputsDTO;
use Core\Input\Application\DTO\OutputFindAllInputsDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\FindAllInputsUseCase;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

test('should be able to list all inputs', function () {
    $dto = new InputFindAllInputsDTO(
        filter: 'a',
        orderBy: 'DESC'
    );
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('findAll')
        ->times(1)
        ->with($dto->filter, $dto->orderBy)
        ->andReturn([
            new Input('input1', 'description'),
            new Input('input2', 'description'),
            new Input('input3', 'description'),
        ]);
    $useCase = new FindAllInputsUseCase(
        repository: $mockRepository
    );
    $response = $useCase->execute(
        input: $dto
    );

    expect($response)->toBeInstanceOf(OutputFindAllInputsDTO::class);
    array_map(fn ($item) => expect($item)->toBeInstanceOf(OutputInputDTO::class), $response->items);
    expect($response->total)->toBe(3);
});

test('should be able to list all inputs - with empty inputs', function () {
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);
    $mockRepository->shouldReceive('findAll')
        ->times(1)
        ->andReturn([]);
    $useCase = new FindAllInputsUseCase(
        repository: $mockRepository
    );
    $response = $useCase->execute(
        input: new InputFindAllInputsDTO(
            filter: 'a',
            orderBy: 'DESC'
        )
    );

    expect($response)->toBeInstanceOf(OutputFindAllInputsDTO::class)
        ->and($response->total)->toBe(0);
});
