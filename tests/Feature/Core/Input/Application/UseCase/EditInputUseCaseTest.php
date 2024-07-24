<?php

use App\Models\Input as Model;

use Core\Input\Application\DTO\EditInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\EditInputUseCase;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use function Pest\Laravel\assertDatabaseHas;

beforeEach(fn () => $this->useCase = new EditInputUseCase(app(InputRepositoryInterface::class)));

test('should edit input', function () {
    $inputFactory = Model::factory()->create();

    $response = $this->useCase->execute(new EditInputDTO(
        id: $inputFactory->id,
        name: 'test name',
        description: 'test description',
    ));

    expect($response)->toBeInstanceOf(OutputInputDTO::class)
        ->and($response->id)->toBe($inputFactory->id)
        ->and($response->name)->toBe('test name')
        ->and($response->description)->toBe('test description');

    assertDatabaseHas('inputs', [
        'id' => $inputFactory->id,
        'name' => 'test name',
        'description' => 'test description',
    ]);
});

test('should throws exception when input not found', function () {
    $this->useCase->execute(new EditInputDTO(
        id: 'fake_id',
        name: 'test name',
        description: 'test description',
    ));
})->throws(EntityNotFoundException::class);
