<?php

namespace App\Api\Dto;

use App\Entity\Employee;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy("all")]
class EmployeeOutput
{
    #[JMS\Expose]
    #[JMS\Type("integer")]
    public int $id;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $name;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $surname;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $email = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $phone = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $description = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $web = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $room = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $photo = null;

    #[JMS\Expose]
    #[JMS\Type("boolean")]
    public bool $isActive;

    #[JMS\Expose]
    #[JMS\Type("array<string>")]
    public array $roles = [];

    #[JMS\Expose]
    #[JMS\Type("array<App\Api\Dto\AccountOutput>")]
    public array $accounts = [];

    public function __construct(int $id, string $name, string $surname, ?string $email = null,
                                ?string $phone = null, ?string $web = null, ?string $room = null,
                                ?string $photo = 'images/default-profile-image.jpg', ?string $description = null,
                                bool $isActive = true, array $roles = [], array $accounts = [])
    {
        $this->id = $id;
        $this->name = $name;
        $this->surname = $surname;
        $this->email = $email;
        $this->phone = $phone;
        $this->web = $web;
        $this->room = $room;
        $this->photo = $photo;
        $this->description = $description;
        $this->isActive = $isActive;
        $this->roles = $roles;
        $this->accounts = $accounts;
    }

    public static function fromEntity(Employee $employee): self
    {
        $roles = [];
        foreach ($employee->getRoles() as $role)
            $roles[] = $role->getTitle();

        $accounts = [];
        foreach ($employee->getAccounts() as $account)
            $accounts[] = AccountOutput::fromEntity($account);

        return new self(
            $employee->getId(),
            $employee->getName(),
            $employee->getSurname(),
            $employee->getEmail(),
            $employee->getPhone(),
            $employee->getWeb(),
            $employee->getRoom(),
            $employee->getPhoto(),
            $employee->getDescription(),
            $employee->isActive(),
            $roles,
            $accounts
        );
    }
}
