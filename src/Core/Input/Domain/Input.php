<?php

namespace Core\Input\Domain;

use Core\SeedWork\Domain\Exceptions\EntityValidationException;
use Core\SeedWork\Domain\Traits\MethodsMagicTrait;
use Core\SeedWork\Domain\Validators\DomainValidator;
use Core\SeedWork\Domain\ValueObjects\Uuid;

class Input
{
    use MethodsMagicTrait;
    protected ?\DateTime $created_at = null;
    protected ?\DateTime $updated_at = null;
    protected ?\DateTime $deleted_at = null;

    /**
     * @throws EntityValidationException
     */
    public function __construct(
        protected string $name,
        protected string $description,
        protected ?Uuid $id = null,


) {
        $this->id = $this->id ?? Uuid::random();
        $this->created_at = new \DateTime();
        $this->updated_at = new \DateTime();
        $this->validate();
    }

    /**
     * @throws EntityValidationException
     */
    public function update(string $name, ?string $description = null): void
    {
        $this->name = $name;
        $this->description = $description ?? $this->description;
        $this->updated_at = new \DateTime();
        $this->validate();
    }

    public function delete(): void
    {
        $this->deleted_at = new \DateTime();
    }

    /**
     * @throws EntityValidationException
     */
    private function validate(): void
    {
        DomainValidator::strMinLength($this->name);
        DomainValidator::strMaxLength($this->name);
        DomainValidator::strMaxLength(
            value: $this->description,
            length: 10000
        );
        DomainValidator::strMinLength(
            value: $this->description,
            length: 5
        );
    }
}
