<?php

namespace App\Api\Dto;

use App\Entity\Account;
use JMS\Serializer\Annotation as JMS;

#[JMS\ExclusionPolicy("all")]
class AccountOutput
{
    #[JMS\Expose]
    #[JMS\Type("integer")]
    public int $id;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $name;

    #[JMS\Expose]
    #[JMS\Type("string")]
    public string $username;

    #[JMS\Expose]
    #[JMS\Type("DateTime")]
    public ?\DateTime $validTo;

    #[JMS\Expose]
    #[JMS\Type("int")]
    public int $employeeId;

    public function __construct(int $id, string $name, string $username, ?\DateTime $validTo, int $employeeId)
    {
        $this->id = $id;
        $this->name = $name;
        $this->username = $username;
        $this->validTo = $validTo;
        $this->employeeId = $employeeId;
    }

    public static function fromEntity(Account $account): self
    {
        return new self(
            $account->getId(),
            $account->getName(),
            $account->getUsername(),
            $account->getValidTo(),
            $account->getEmployee()->getId()
        );
    }
}
