<?php

use Core\Input\Domain\Input;
use Core\SeedWork\Domain\Exceptions\EntityValidationException;
use Core\SeedWork\Domain\ValueObjects\Uuid;
use Faker\Factory;

test('should access all properties', function () {
    $input = new Input(
        name: 'Input name',
        description: 'desc Input'
    );
    expect($input->name)->toBe('Input name')
        ->and($input->description)->toBe('desc Input')
        ->and($input->id)->not->toBeNull()
        ->and($input->id())->toBeString()
        ->and($input->name)->toBeString()
        ->and($input->description)->toBeString();
});

test('should use uuid passed', function () {
    $id = Uuid::random();
    $input = new Input(
        name: 'input name',
        description: 'desc input',
        id: $id
    );
    expect($input->id)->toBe($id)
        ->and($input->id())->toBe((string) $id);
});

test('should throws exceptions when name is wrong - less 2', function () {
    new Input(
        name: 'a',
        description: 'desc test',
    );
})->throws(EntityValidationException::class, 'The value must be at least 3 characters');

test('should throws exceptions when name is wrong - more 255', function () {
    $name = Factory::create()->sentence(255);
    new Input(
        name: $name,
        description: 'desc test',
    );
})->throws(EntityValidationException::class, 'The value must not be greater than 255 characters');

test('should throws exceptions when description is wrong - less 10', function () {
    new Input(
        name: 'Input platinum',
        description: 'desc',
    );
})->throws(EntityValidationException::class, 'The value must be at least 5 characters');

test('should throws exceptions when description is wrong - more 10000', function () {
    $description = Factory::create()->sentence(10000);
    new Input(
        name: 'Input premium',
        description: $description,
    );
})->throws(EntityValidationException::class, 'The value must not be greater than 10000 characters');

test('should update values entity', function () {
    $input = new Input(
        name: 'Input premium',
        description: 'description of Input'
    );
    $input->update(
        name: 'name updated',
        description: 'desc updated'
    );
    expect($input->name)->toBe('name updated')
        ->and($input->description)->toBe('desc updated');

    $input->update(
        name: 'name updated(2)'
    );
    expect($input->name)->toBe('name updated(2)')
        ->and($input->description)->toBe('desc updated');
});

test('should throws exception when update entity with wrong name', function () {
    $input = new Input(
        name: 'input premium',
        description: 'description of input'
    );
    $input->update(
        name: 'na',
        description: 'desc updated'
    );
})->throws(EntityValidationException::class);

test('should throws exception when update entity with wrong description', function () {
    $input = new Input(
        name: 'Input premium',
        description: 'description of Input'
    );
    $input->update(
        name: 'input premium',
        description: 'desc'
    );
})->throws(EntityValidationException::class);
