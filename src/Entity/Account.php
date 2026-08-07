<?php

namespace App\Entity;

use App\Repository\AccountRepository;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Length(max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[Assert\NotBlank]
    #[Assert\Length(min:8, max: 255)]
    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[Assert\Type(DateTimeInterface::class)]
    #[ORM\Column(nullable: true)]
    private ?\DateTime $validTo = null;

    #[Assert\NotNull]
    #[ORM\ManyToOne(targetEntity: Employee::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Employee $employee = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getValidTo(): ?\DateTime
    {
        return $this->validTo;
    }

    public function setValidTo(?\DateTime $validTo): static
    {
        $this->validTo = $validTo;

        return $this;
    }

    public function getEmployee(): ?Employee
    {
        return $this->employee;
    }

    public function setEmployee(?Employee $employee): static
    {
        if ($this->employee === $employee)
            return $this;

        if ($this->employee !== null) {
            $before = $this->employee;
            $this->employee = null;
            $before->removeAccount($this);
        }

        $this->employee = $employee;
        if ($employee !== null && !$employee->getAccounts()->contains($this))
            $employee->addAccount($this);

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function isPermanent(): bool
    {
        return $this->validTo === null;
    }
}
