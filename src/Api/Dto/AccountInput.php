<?php

namespace App\Api\Dto;

use App\Entity\Account;
use App\Entity\Employee;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy("all")]
class AccountInput
{
    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $name;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $username;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $password;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public ?string $validTo = null;

    #[JMS\Expose]
    #[JMS\Type("int")]
    public int $employeeId;

    public function toEntity(?Employee $employee = null, ?Account $account = null): Account
    {
        if ($account === null)
            $account = new Account();

        $account->setName($this->name);
        $account->setUsername($this->username);
        $account->setPassword($this->password);
        $account->setValidTo($this->validTo ? new \DateTime($this->validTo) : null);

        if ($employee !== null)
            $account->setEmployee($employee);

        return $account;
    }
}
