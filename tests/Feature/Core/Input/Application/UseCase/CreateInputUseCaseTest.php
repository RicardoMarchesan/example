<?php

use Core\Input\Application\DTO\CreateInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\CreateInputUseCase;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use function Pest\Laravel\assertDatabaseHas;

test('should create input', function () {
    $repository = app(InputRepositoryInterface::class);
    $useCase = new CreateInputUseCase($repository);

    $response = $useCase->execute(new CreateInputDTO('name', 'description'));
    expect($response)->toBeInstanceOf(OutputInputDTO::class)
        ->and($response->name)->toBe('name')
        ->and($response->description)->toBe('description')
        ->and($response->id)->not->toBeNull();

    assertDatabaseHas('inputs', ['name' => 'name', 'description' => 'description']);
});
