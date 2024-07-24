<?php

use App\Models\Input as Model;
use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\UseCase\GetInputUseCase;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;

beforeEach(fn () => $this->useCase = new GetInputUseCase(app(InputRepositoryInterface::class)));

test('should return input', function () {
    $InputFactory = Model::factory()->create();

    $response = $this->useCase->execute(new InputInputDTO(id: $InputFactory->id));

    expect($response)->toBeInstanceOf(OutputInputDTO::class)
        ->and($response->id)->toBe($InputFactory->id)
        ->and($response->name)->toBe($InputFactory->name)
        ->and($response->description)->toBe($InputFactory->description);
});

test('should throws exception when input not found', function () {
    $this->useCase->execute(new InputInputDTO(id: 'fake_id'));
})->throws(EntityNotFoundException::class);
