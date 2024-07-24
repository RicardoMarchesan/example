<?php

namespace App\Repositories\Eloquent;

use App\Adapters\PaginationEloquentAdapter;
use App\Models\Input as Model;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Core\SeedWork\Domain\Exceptions\EntityNotFoundException;
use Core\SeedWork\Domain\Exceptions\EntityValidationException;
use Core\SeedWork\Domain\Repositories\PaginationInterface;
use Core\SeedWork\Domain\ValueObjects\Uuid;

class InputRepository implements InputRepositoryInterface
{
    public function __construct(protected Model $model) {}

    /**
     * @throws EntityValidationException
     */
    public function insert(Input $input): Input
    {
        $model = $this->model->create([
            'id' => (string) $input->id(),
            'name' => $input->name,
            'description' => $input->description,
        ]);

        return $this->convertModelToEntity($model);
    }

    /**
     * @throws EntityNotFoundException
     * @throws EntityValidationException
     */
    public function findById(string $id, bool $withTrashed = false): Input
    {

        $query = $withTrashed ? $this->model->withTrashed() : $this->model;

        $model = $query->find($id);

        if (!$model) {
            throw new EntityNotFoundException('Input not found');
        }

        return $this->convertModelToEntity($model);
    }

    /**
     * @return Input[]
     *
     * @throws EntityValidationException
     */
    public function findAll(string $filter = '', string $orderBy = 'DESC'): array
    {
        $response = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter !== '') {
                    $query->where('name', $filter);
                    $query->orWhere('description', 'like', "%{$filter}%");
                }
            })
            ->orderBy('name', $orderBy)
            ->get();

        return $response->map(fn (Model $model) => $this->convertModelToEntity($model))->toArray();
    }

    public function paginate(string $filter = '', string $orderBy = 'DESC', int $page = 1, int $totalPerPage = 15): PaginationInterface
    {
        $results = $this->model
            ->where(function ($query) use ($filter) {
                if ($filter !== '') {
                    $query->whereName($filter);
                    $query->orWhere('description', 'like', "%{$filter}%");
                }
            })
            ->orderBy('name', $orderBy)
            ->paginate($totalPerPage, ['*'], 'page', $page);

        return new PaginationEloquentAdapter($results);
    }

    /**
     * @throws EntityValidationException
     */
    public function update(Input $input): ?Input
    {
        if (! $model = $this->model->find($input->id())) {
            return null;
        }
        $model->update([
            'name' => $input->name,
            'description' => $input->description,
        ]);

        return $this->convertModelToEntity($model);
    }

    public function delete(string $id): bool
    {
        if (! $model = $this->model->find($id)) {
            return false;
        }
        $model->delete();

        return true;
    }

    /**
     * @throws EntityValidationException
     */
    private function convertModelToEntity(Model $model): Input
    {
        return new Input(
            name: $model->name,
            description: $model->description,
            id: new Uuid($model->id)
        );
    }
}
