<?php

use Core\Input\Application\DTO\DeleteOutputInputDTO;
use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Application\UseCase\DeleteInputUseCase;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use App\Models\Input as Model;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\assertSoftDeleted;

beforeEach(fn () => $this->useCase = new DeleteInputUseCase(app(InputRepositoryInterface::class)));

test('should delete input', function () {
    $model = Model::factory()->create();

    $response = $this->useCase->execute(new InputInputDTO(id: $model->id));

    expect($response)->toBeInstanceOf(DeleteOutputInputDTO::class)
        ->and($response->deleted)->toBeTrue();
    assertSoftDeleted('inputs', ['id' => $model->id]);
});

test('should throws exception when input not found', function () {
    $this->useCase->execute(new InputInputDTO(id: 'fake_id'));
})->throws(EntityNotFoundException::class);
