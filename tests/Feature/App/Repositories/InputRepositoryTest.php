<?php

use App\Models\Input as Model;
use App\Repositories\Eloquent\InputRepository;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use Core\SeedWork\Domain\ValueObjects\Uuid;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;

beforeEach(fn () => $this->repository = new InputRepository(new Model));

test('should insert input in database', function () {
    $input = new Input(
        name: 'input test',
        description: 'description test'
    );
    $entity = $this->repository->insert($input);

    expect($this->repository)->toBeInstanceOf(InputRepositoryInterface::class);
    assertDatabaseHas('inputs', [
        'id' => $input->id,
        'name' => $input->name,
        'description' => $input->description,
    ]);
    expect($input->id())->toBe($entity->id())
        ->and($input->name)->toBe($entity->name)
        ->and($input->description)->toBe($entity->description);
});

test('should throws exception when not found input', function () {
    $this->repository->findById('fake');
})->throws(EntityNotFoundException::class, 'Input not found');

test('should return entity', function () {
    $inputFactory = Model::factory()->create();
    $entity = $this->repository->findById($inputFactory->id);

    expect($entity->id())->toBe($inputFactory->id)
        ->and($entity->name)->toBe($inputFactory->name)
        ->and($entity->description)->toBe($inputFactory->description);
});

test('should return empty array when not exists inputs', function () {
    $entities = $this->repository->findAll();
    expect($entities)->toBe([]);
});

test('should return array of entity input', function () {
    Model::factory()->count(10)->create();
    $entities = $this->repository->findAll();
    expect(count($entities))->toBe(10);
    array_map(fn (Input $input) => expect($input)->toBeInstanceOf(Input::class), $entities);
});

test('should return array of entity input - with filter', function () {
    Model::factory()->count(10)->create();
    Model::factory()->count(10)->create(['name' => 'input test filter']);
    $entities = $this->repository->findAll(
        filter: 'input test filter'
    );
    expect(count($entities))->toBe(10);
    array_map(fn (Input $input) => expect($input)->toBeInstanceOf(Input::class), $entities);
});

test('should return false when try remove input not found', function () {
    expect($this->repository->delete('fake'))->toBeFalse();
});

test('should return true when remove input', function () {
    $model = Model::factory()->create();

    expect($this->repository->delete($model->id))->toBeTrue();
    assertSoftDeleted('inputs', ['id' => $model->id]);
});

test('should return null when not found input', function () {
    $input = new Input('name', 'description');
    expect($this->repository->update($input))->toBeNull();
});

test('should update input', function () {
    $model = Model::factory()->create();
    $input = new Input(
        name: 'new name',
        description: 'new description',
        id: new Uuid($model->id)
    );
    $entity = $this->repository->update($input);

    expect($entity->name)->toBe($entity->name)
        ->and($entity->description)->toBe($entity->description);
    assertDatabaseHas('inputs', [
        'id' => $model->id,
        'name' => $input->name,
        'description' => $input->description,
    ]);
});

test('should return empty array when not exists inputs - paginate', function () {
    $pagination = $this->repository->paginate();
    expect($pagination->items())->toBe([])
        ->and($pagination->firstPage())->toBe(null);
});

test('should return inputs with paginate', function () {
    Model::factory()->count(100)->create();

    $pagination = $this->repository->paginate();

    expect(count($pagination->items()))->toBe(15)
        ->and($pagination->total())->toBe(100)
        ->and($pagination->lastPage())->toBe(7)
        ->and($pagination->firstPage())->toBe(1)
        ->and($pagination->totalPerPage())->toBe(15)
        ->and($pagination->nextPage())->toBe(2)
        ->and($pagination->previousPage())->toBe(null)
        ->and($pagination->currentPage())->toBe(1);
    array_map(fn ($entity) => expect($entity)->toBeInstanceOf(stdClass::class), $pagination->items());
});

test('should paginate with total 10 items per page', function () {
    Model::factory()->count(100)->create();

    $pagination = $this->repository->paginate(totalPerPage: 10);

    expect(count($pagination->items()))->toBe(10)
        ->and($pagination->total())->toBe(100)
        ->and($pagination->lastPage())->toBe(10)
        ->and($pagination->firstPage())->toBe(1)
        ->and($pagination->totalPerPage())->toBe(10)
        ->and($pagination->nextPage())->toBe(2)
        ->and($pagination->previousPage())->toBe(null)
        ->and($pagination->currentPage())->toBe(1);
    array_map(fn ($entity) => expect($entity)->toBeInstanceOf(stdClass::class), $pagination->items());
});

test('should paginate with filter', function () {
    Model::factory()->count(50)->create();
    Model::factory()->count(50)->create(['name' => 'input filter']);

    $pagination = $this->repository->paginate(filter: 'input filter');

    expect(count($pagination->items()))->toBe(15)
        ->and($pagination->total())->toBe(50)
        ->and($pagination->lastPage())->toBe(4)
        ->and($pagination->firstPage())->toBe(1)
        ->and($pagination->totalPerPage())->toBe(15)
        ->and($pagination->nextPage())->toBe(2)
        ->and($pagination->previousPage())->toBe(null)
        ->and($pagination->currentPage())->toBe(1);
    array_map(fn ($entity) => expect($entity)->toBeInstanceOf(stdClass::class), $pagination->items());

    $pagination = $this->repository->paginate(filter: 'input filter', totalPerPage: 10);

    expect(count($pagination->items()))->toBe(10)
        ->and($pagination->total())->toBe(50)
        ->and($pagination->lastPage())->toBe(5)
        ->and($pagination->firstPage())->toBe(1)
        ->and($pagination->totalPerPage())->toBe(10)
        ->and($pagination->nextPage())->toBe(2)
        ->and($pagination->previousPage())->toBe(null)
        ->and($pagination->currentPage())->toBe(1);
    array_map(fn ($entity) => expect($entity)->toBeInstanceOf(stdClass::class), $pagination->items());
});

test('should paginate with custom page', function () {
    Model::factory()->count(100)->create();

    $pagination = $this->repository->paginate(page: 2);

    expect(count($pagination->items()))->toBe(15)
        ->and($pagination->total())->toBe(100)
        ->and($pagination->lastPage())->toBe(7)
        ->and($pagination->firstPage())->toBe(1)
        ->and($pagination->totalPerPage())->toBe(15)
        ->and($pagination->nextPage())->toBe(3)
        ->and($pagination->previousPage())->toBe(1)
        ->and($pagination->currentPage())->toBe(2);
    array_map(fn ($entity) => expect($entity)->toBeInstanceOf(stdClass::class), $pagination->items());
});
