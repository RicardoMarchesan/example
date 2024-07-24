<?php

namespace Core\Input\Application\DTO;

use Core\Input\Domain\Input;

class OutputInputDTO
{
    public function __construct(
        public string $id,
        public string $name,
        public string $description,
        public ?string $created_at = null,
        public ?string $updated_at = null,
        public ?string $deleted_at = null,
    ) {}

    public static function fromEntity(Input $input): self
    {
        return new self(
            id: $input->id(),
            name: $input->name,
            description: $input->description,
            created_at: $input->created_at?->format('Y-m-d H:i:s'),
            updated_at: $input->updated_at?->format('Y-m-d H:i:s'),
            deleted_at: $input->deleted_at?->format('Y-m-d H:i:s')
        );
    }
}
