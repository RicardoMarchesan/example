<?php

use App\Models\Input;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\deleteJson;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;
use function Pest\Laravel\putJson;

test('should get all inputs - with empty inputs', function () {
    getJson(route('inputs.index'))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [],
            'meta' => [
                'total',
                'last_page',
                'first_page',
                'next_page',
                'previous_page',
                'total_per_page',
                'current_page',
            ],
        ]);
});

test('should get paginate inputs', function () {
    Input::factory(20)->create();
    getJson(route('inputs.index'))
        ->assertStatus(200)
        ->assertJsonCount(15, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
            'meta' => [
                'total',
                'last_page',
                'first_page',
                'next_page',
                'previous_page',
            ],
        ]);
});

test('should get paginate inputs - page 2', function () {
    Input::factory(20)->create();
    getJson(route('inputs.index') . '?page=2')
        ->assertStatus(200)
        ->assertJsonCount(5, 'data')
        ->assertJsonStructure([
            'data' => [
                '*' => [
                    'id',
                    'name',
                    'description',
                ],
            ],
            'meta' => [
                'total',
                'last_page',
                'first_page',
                'next_page',
                'previous_page',
            ],
        ]);
});

test('should get paginate inputs - total per page', function () {
    Input::factory(20)->create();
    getJson(route('inputs.index') . '?per_page=20')
        ->assertOk()
        ->assertJsonCount(20, 'data');
});

test('should get paginate inputs - with filter', function () {
    Input::factory(10)->create();
    Input::factory(10)->create(['name' => 'custom']);
    getJson(route('inputs.index') . '?filter=custom')
        ->assertOk()
        ->assertJsonCount(10, 'data');
});

test('should create new input', function () {
    postJson(
        uri: route('inputs.store'),
        data: [
            'name' => 'test name',
            'description' => 'test description',
        ],
        headers: ['Accept' => 'application/json']
    )->assertCreated()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ]
        ]);
});

describe('validations', function () {
    test('should validate create input', function () {
        postJson(
            uri: route('inputs.store'),
            data: [],
            headers: ['Accept' => 'application/json']
        )->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'description',
            ]);
    });
    test('should validate update input', function () {
        $input = Input::factory()->create();
        putJson(
            uri: route('inputs.update', $input->id),
            data: []
        )->assertStatus(422)
            ->assertJsonValidationErrors([
                'name',
                'description',
            ]);
    });
});

test('should return input by id', function () {
    $input = Input::factory()->create();

    getJson(route('inputs.show', $input->id))
        ->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
        ]);
});

test('should return 404 when input not found', function () {
    getJson(route('inputs.show', 'fake_id'))->assertNotFound();
});

test('should update input', function () {
    $input = Input::factory()->create();
    log($input['data']);
    $response = putJson(
        uri: route('inputs.update', $input->id),
        data: [
            'name' => 'update name',
            'description' => 'update description',
        ]
    )->assertOk()
        ->assertJsonStructure([
            'data' => [
                'id',
                'name',
                'description',
            ],
        ]);
    expect($response['data']['name'])->toBe('update name')
        ->and($response['data']['description'])->toBe('update description');
    assertDatabaseHas('inputs', [
        'id' => $input->id,
        'name' => 'update name',
        'description' => 'update description',
    ]);
});

test('should return 404 when try update input not found', function () {
    putJson(
        uri:route('inputs.update', 'fake_id'),
        data: [
            'name' => 'update name',
            'description' => 'update description',
        ],
    )->assertNotFound();
});

test('should delete input', function () {
    $input = Input::factory()->create();

    deleteJson(
        uri: route('inputs.destroy', $input->id)
    )->assertNoContent();

//    dd(\App\Models\Input::withTrashed()->find($input->id));
//    dd(getJson(route('inputs.show', ['input' => $input->id, 'with_trashed' => true])));

    assertSoftDeleted('inputs', ['id' => $input->id]);
});

test('should return 404 when try delete input not found', function () {
    deleteJson(route('inputs.destroy', 'fake_id'))->assertNotFound();
});
