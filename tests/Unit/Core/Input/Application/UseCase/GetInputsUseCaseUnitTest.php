<?php

use Core\Input\Application\DTO\InputInputsDTO;
use Core\Input\Application\DTO\OutputInputDTO;
use Core\Input\Application\DTO\OutputInputsDTO;
use Core\Input\Application\UseCase\GetInputsUseCase;
use Core\Input\Domain\Input;
use Core\Input\Domain\Repositories\InputRepositoryInterface;
use Tests\Stubs\PaginationStub;

test('should get all inputs', function () {
    $inputDto = new InputInputsDTO(
        filter: 'filter',
        orderBy: 'ASC',
        page: 2,
        totalPerPage: 10
    );
    $stubPagination = new PaginationStub(
        items: [
            new Input('name1', 'description'),
            new Input('name2', 'description'),
            new Input('name3', 'description'),
            new Input('name4', 'description'),
        ]
    );
    $mockRepository = Mockery::mock(InputRepositoryInterface::class);

    $mockRepository->shouldReceive('paginate')
        ->times(1)
        ->with($inputDto->filter, $inputDto->orderBy, $inputDto->page, $inputDto->totalPerPage)
        ->andReturn($stubPagination);
    $useCase = new GetInputsUseCase(
        repository: $mockRepository
    );

    $response = $useCase->execute(input: $inputDto);
    expect($response)->toBeInstanceOf(OutputInputsDTO::class)
        ->and($response->items)->toBeArray();

    array_map(fn ($item) => expect($item)->toBeInstanceOf(OutputInputDTO::class), $response->items);

    expect($response->last_page)->toBe(1)
        ->and($response->first_page)->toBe(1)
        ->and($response->total_per_page)->toBe(15)
        ->and($response->next_page)->toBe(1)
        ->and($response->previous_page)->toBe(1);
});
