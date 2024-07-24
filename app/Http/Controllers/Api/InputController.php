<?php

namespace App\Http\Controllers\Api;

use App\Adapters\ApiAdapter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Inputs\StoreInputRequest;
use App\Http\Requests\Inputs\UpdateInputRequest;
use App\Http\Resources\InputResource;
use Core\Input\Application\DTO\CreateInputDTO;
use Core\Input\Application\DTO\EditInputDTO;
use Core\Input\Application\DTO\InputInputDTO;
use Core\Input\Application\DTO\InputInputsDTO;
use Core\Input\Application\UseCase\CreateInputUseCase;
use Core\Input\Application\UseCase\DeleteInputUseCase;
use Core\Input\Application\UseCase\EditInputUseCase;
use Core\Input\Application\UseCase\GetInputUseCase;
use Core\Input\Application\UseCase\GetInputsUseCase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class InputController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(GetInputsUseCase $useCase, Request $request): AnonymousResourceCollection
    {
        $inputs = $useCase->execute(new InputInputsDTO(
            filter: $request->input('filter', ''),
            orderBy: $request->input('order_by', 'DESC'),
            page: $request->input('page', 1),
            totalPerPage: $request->input('per_page', 15),
        ));

        return (InputResource::collection(collect($inputs->items))->additional([
            'meta' => [
                'total' => $inputs->total,
                'last_page' => $inputs->last_page,
                'first_page' => $inputs->first_page,
                'next_page' => $inputs->next_page,
                'previous_page' => $inputs->previous_page,
                'current_page' => $inputs->current_page,
                'total_per_page' => $inputs->total_per_page,
            ]
        ]));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreInputRequest $request, CreateInputUseCase $useCase): JsonResponse
    {
        $input = $useCase->execute(new CreateInputDTO(
            name: $request->name,
            description: $request->description,
        ));

        return (new InputResource($input))
            ->response()
            ->setStatusCode(ResponseAlias::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(GetInputUseCase $useCase, Request $request, string $id): InputResource
    {
        $withTrashed = $request->query('with_trashed', false);
        $input = $useCase->execute(new InputInputDTO(id: $id), (bool) $withTrashed);

        return new InputResource($input);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EditInputUseCase $useCase, UpdateInputRequest $request, string $id): InputResource
    {

        $input = $useCase->execute(new EditInputDTO(
            id: $id,
            name: $request->name,
            description: $request->description,
        ));

        return new InputResource($input);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DeleteInputUseCase $useCase, string $id): JsonResponse
    {
        $response = $useCase->execute(new InputInputDTO(id: $id));

        return response()->json([
            'deleted' => $response->deleted
        ], $response->deleted ? ResponseAlias::HTTP_NO_CONTENT : ResponseAlias::HTTP_BAD_REQUEST);
    }
}
