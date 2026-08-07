<?php

namespace App\Api\Dto;

use App\Entity\Employee;
use App\Service\RoleService;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy("all")]
class EmployeeInput
{
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
    public ?string $web = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $room = null;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $photo = 'images/default-profile-image.jpg';

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $description = null;

    #[JMS\Expose]
    #[JMS\Type("boolean")]
    public ?bool $isActive = null;

    #[JMS\Expose]
    #[JMS\Type("array<int>")]
    public ?array $roleIds = [];

    public function toEntity(RoleService $roleService, ?Employee $employee = null): Employee
    {
        if ($employee === null)
            $employee = new Employee();

        $employee->setName($this->name);
        $employee->setSurname($this->surname);
        $employee->setEmail($this->email);
        $employee->setPhone($this->phone);
        $employee->setWeb($this->web);
        $employee->setRoom($this->room);
        $employee->setPhoto($this->photo);
        $employee->setDescription($this->description);

        if ($this->isActive !== null)
            $employee->setIsActive($this->isActive);
        else
            $employee->setIsActive(true);

        if (is_array($this->roleIds)) {
            foreach ($employee->getRoles()->toArray() as $role)
                $employee->removeRole($role);

            foreach ($this->roleIds as $roleId) {
                $role = $roleService->findById($roleId);
                if ($role)
                    $employee->addRole($role);
            }
        }

        return $employee;
    }
}
