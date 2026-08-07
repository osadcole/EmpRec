<?php

namespace App\Service;

use App\Entity\Role;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;

class RoleService
{
    public function __construct(
        private RoleRepository $roleRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function findById(int $id)
    {
        return $this->roleRepository->find($id);
    }

    public function save(Role $role): void
    {
        $this->entityManager->persist($role);
        $this->entityManager->flush();
    }
}
