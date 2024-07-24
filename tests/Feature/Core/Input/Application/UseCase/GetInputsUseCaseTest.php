<?php

use App\Models\Input as Model;
use Core\Input\Application\DTO\InputInputsDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\DTO\OutputInputsDTO;
use Core\Input\Application\UseCase\GetInputsUseCase;
use Core\Input\Domain\Repositories\InputRepositoryInterface;

beforeEach(fn () => $this->useCase = new GetInputsUseCase(app(InputRepositoryInterface::class)));

test('should get inputs with paginate', function () {
    Model::factory()->count(50)->create();

    $response = $this->useCase->execute(new InputInputsDTO());

    expect($response)->toBeInstanceOf(OutputInputsDTO::class)
        ->and(count($response->items))->toBe(15)
        ->and($response->total)->toBe(50)
        ->and($response->last_page)->toBe(4)
        ->and($response->first_page)->toBe(1)
        ->and($response->next_page)->toBe(2)
        ->and($response->previous_page)->toBeNull();
    array_map(fn ($dtoOutput) => expect($dtoOutput)->toBeInstanceOf(OutputInputDTO::class), $response->items);
});

test('should get inputs with paginate (page 2)', function () {
    Model::factory()->count(50)->create();

    $response = $this->useCase->execute(new InputInputsDTO(
        page: 2
    ));

    expect(count($response->items))->toBe(15)
        ->and($response->total)->toBe(50)
        ->and($response->last_page)->toBe(4)
        ->and($response->first_page)->toBe(1)
        ->and($response->next_page)->toBe(3)
        ->and($response->previous_page)->toBe(1);
});

test('should get inputs with paginate (with filter)', function () {
    Model::factory()->count(25)->create();
    Model::factory()->count(25)->create(['name' => 'input filter']);

    $response = $this->useCase->execute(new InputInputsDTO(
        filter: 'input filter'
    ));

    expect(count($response->items))->toBe(15)
        ->and($response->total)->toBe(25)
        ->and($response->last_page)->toBe(2)
        ->and($response->first_page)->toBe(1)
        ->and($response->next_page)->toBe(2)
        ->and($response->previous_page)->toBeNull();
});

test('should get inputs with paginate (with total per page)', function () {
    Model::factory()->count(60)->create();

    $response = $this->useCase->execute(new InputInputsDTO(
        totalPerPage: 20
    ));

    expect(count($response->items))->toBe(20)
        ->and($response->total)->toBe(60)
        ->and($response->last_page)->toBe(3)
        ->and($response->first_page)->toBe(1)
        ->and($response->next_page)->toBe(2)
        ->and($response->previous_page)->toBeNull();
});

test('should get inputs with paginate (empty)', function () {
    $response = $this->useCase->execute(new InputInputsDTO());

    expect(count($response->items))->toBe(0)
        ->and($response->total)->toBe(0)
        ->and($response->last_page)->toBe(1)
        ->and($response->first_page)->toBeNull()
        ->and($response->next_page)->toBeNull()
        ->and($response->previous_page)->toBeNull();
});
