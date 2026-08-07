<?php

namespace App\Service;


use App\Entity\Account;
use App\Entity\Employee;
use App\Repository\AccountRepository;
use Doctrine\ORM\EntityManagerInterface;

class AccountService
{
    public function __construct(
        private AccountRepository $accountRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function paginate(int $page, int $limit, Employee $employee): array
    {
        $qb = $this->accountRepository->createQueryBuilder('a')->andWhere('a.employee = :employee')
            ->setParameter('employee', $employee)->orderBy('a.id', 'ASC')
            ->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);

        $accounts = $qb->getQuery()->getResult();

        $accountsCnt = $this->accountRepository->createQueryBuilder('a')->select('COUNT(a.id)')
            ->andWhere('a.employee = :employee')->setParameter('employee', $employee)
            ->getQuery()->getSingleScalarResult();

        return [
            'accounts' => $accounts,
            'accountsCnt' => $accountsCnt
        ];
    }

    public function findById(int $id)
    {
        return $this->accountRepository->find($id);
    }

    public function save(Account $account): void
    {
        $this->entityManager->persist($account);
        $this->entityManager->flush();
    }

    public function delete(Account $account): void
    {
        $this->entityManager->remove($account);
        $this->entityManager->flush();
    }
}
