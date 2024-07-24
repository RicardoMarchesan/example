<?php

use App\Models\Input as Model;
use Core\Input\Application\DTO\InputFindAllInputsDTO;
use Core\Input\Application\DTO\OutputFindAllInputsDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\FindAllInputsUseCase;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

beforeEach(fn () => $this->useCase = new FindAllInputsUseCase(app(InputRepositoryInterface::class)));

test('should return all inputs', function () {
    Model::factory(10)->create();

    $response = $this->useCase->execute(new InputFindAllInputsDTO());

    expect($response)->toBeInstanceOf(OutputFindAllInputsDTO::class)
        ->and($response->total)->toBe(10);
    array_map(fn ($entity) => expect($entity)->toBeInstanceOf(OutputInputDTO::class), $response->items);
});

test('should return all inputs - with filter', function () {
    Model::factory(10)->create();
    Model::factory(10)->create(['name' => 'input filter']);

    $response = $this->useCase->execute(new InputFindAllInputsDTO(
        filter: 'input filter'
    ));

    expect($response->total)->toBe(10);
    array_map(fn ($entity) => expect($entity->name)->toBe('input filter'), $response->items);
});

test('should return all inputs - empty', function () {
    $response = $this->useCase->execute(new InputFindAllInputsDTO());

    expect($response)->toBeInstanceOf(OutputFindAllInputsDTO::class)
        ->and($response->total)->toBe(0);
});
