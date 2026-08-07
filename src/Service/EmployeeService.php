<?php

namespace App\Service;

use App\Entity\Account;
use App\Entity\Employee;
use App\Repository\EmployeeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class EmployeeService
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmployeeRepository $employeeRepository,
        private SluggerInterface $slugger,
        public ?string $name = null,
        public ?string $surname = null,
        public ?string $phone = null,
        public ?string $email = null,
        public ?string $web = null,
        public ?string $room = null,
        public ?array $roles = null,
        public ?string $sort = 'full name',
        public ?string $sortDirection = 'ASC',
    ) {}

    public function findById(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function findNewest(int $limit = 10)
    {
        return $this->employeeRepository->createQueryBuilder('e')->orderBy('e.id', 'DESC')
            ->setMaxResults($limit)->getQuery()->getResult();
    }

    public function save(Employee $employee, ?UploadedFile $imageFile = null,
                         ?string $imagesDirectory = '%kernel.project_dir%/public/uploads/images'): void
    {
        if ($imageFile) {
            $originalImageName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeImageName = $this->slugger->slug($originalImageName);
            $newImageName = $safeImageName.'-'.uniqid().'.'.$imageFile->guessExtension();
            $imageFile->move($imagesDirectory, $newImageName);
            $employee->setPhoto(ltrim('uploads/images/' . $newImageName));
        }

        $employee->setIsActive(true);
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }

    public function processFormData(?array $formData)
    {
        $formData = $formData ?? [];

        $this->name = isset($formData['name']) && $formData['name'] !== '' ? $formData['name'] : null;
        $this->surname = isset($formData['surname']) && $formData['surname'] !== '' ? $formData['surname'] : null;
        $this->phone = isset($formData['phone']) && $formData['phone'] !== '' ? $formData['phone'] : null;
        $this->email = isset($formData['email']) && $formData['email'] !== '' ? $formData['email'] : null;
        $this->web = isset($formData['web']) && $formData['web'] !== '' ? $formData['web'] : null;
        $this->room = isset($formData['room']) && $formData['room'] !== '' ? $formData['room'] : null;
        $roles = $formData['role'] ?? null;
        $roles = $roles !== null ? $roles->toArray() : null;
        $this->roles = !empty($roles) ? $roles : null;
        $this->sort = isset($formData['sort']) && $formData['sort'] !== '' ? $formData['sort'] : 'full name';
        $this->sortDirection = isset($formData['sortDirection']) && $formData['sortDirection'] !== '' ? $formData['sortDirection'] : 'ASC';

        return $this;
    }

    public function searchApi(?array $searchQuery, ?RoleService $roleService = null)
    {
        $searchQuery = $searchQuery ?? [];

        $this->name = isset($searchQuery['name']) && $searchQuery['name'] !== '' ? $searchQuery['name'] : null;
        $this->surname = isset($searchQuery['surname']) && $searchQuery['surname'] !== '' ? $searchQuery['surname'] : null;
        $this->phone = isset($searchQuery['phone']) && $searchQuery['phone'] !== '' ? $searchQuery['phone'] : null;
        $this->email = isset($searchQuery['email']) && $searchQuery['email'] !== '' ? $searchQuery['email'] : null;
        $this->web = isset($searchQuery['web']) && $searchQuery['web'] !== '' ? $searchQuery['web'] : null;
        $this->room = isset($searchQuery['room']) && $searchQuery['room'] !== '' ? $searchQuery['room'] : null;
        $roles = $searchQuery['role'] ?? [];

        $roleEntities = [];
        foreach ($roles as $roleId) {
            $role = $roleService->findById($roleId);
            if ($role)
                $roleEntities[] = $role;
        }

        $this->roles = !empty($roleEntities) ? $roleEntities : null;
        $this->sort = isset($searchQuery['sort']) && $searchQuery['sort'] !== '' ? $searchQuery['sort'] : $this->sort;
        $this->sortDirection = isset($searchQuery['sortDirection']) && $searchQuery['sortDirection'] !== ''
                                ? $searchQuery['sortDirection'] : $this->sortDirection;

        return $this;
    }

    public function search (int $page, int $limit)
    {
        $qb = $this->employeeRepository->createQueryBuilder('e');

        if ($this->name !== null)
            $qb = $qb->andWhere('e.name LIKE :name')
                ->setParameter('name', '%'.$this->name.'%');

        if ($this->surname !== null)
            $qb = $qb->andWhere('e.surname LIKE :surname')
                ->setParameter('surname', '%'.$this->surname.'%');

        if ($this->phone !== null)
            $qb = $qb->andWhere('e.phone LIKE :phone')
                ->setParameter('phone', '%'.$this->phone.'%');

        if ($this->email !== null)
            $qb = $qb->andWhere('e.email LIKE :email')
                ->setParameter('email', '%'.$this->email.'%');

        if ($this->web !== null)
            $qb = $qb->andWhere('e.web LIKE :web')
                ->setParameter('web', '%'.$this->web.'%');

        if ($this->room !== null)
            $qb = $qb->andWhere('e.room LIKE :room')
                ->setParameter('room', '%'.$this->room.'%');

        if ($this->roles !== null)
            $qb = $qb->join('e.roles', 'r')
                ->andWhere('r.id IN (:roles)')
                ->setParameter('roles', array_map(function($r) {
                    return $r->getId();
                }, $this->roles));

        $qbCnt = clone $qb;
        $employeesCnt = $qbCnt->select('COUNT(DISTINCT e.id)')
            ->getQuery()->getSingleScalarResult();


        if ($this->sort === 'full name')
            $qb->orderBy('e.surname', $this->sortDirection)->addOrderBy('e.name', $this->sortDirection);
        else
            $qb->orderBy('e.' . $this->sort, $this->sortDirection);

        $qb->setFirstResult(($page - 1) * $limit)->setMaxResults($limit);
        $employees = $qb->getQuery()->getResult();

        return [
            'employees' => $employees,
            'employeesCnt' => $employeesCnt,
        ];
    }

    public function deactivate(Employee $employee)
    {
        foreach ($employee->getAccounts() as $account) {
            if ($account->isPermanent()) {
                $this->entityManager->remove($account);
            } else {
                $account->setValidTo(new \DateTime());
                $this->entityManager->persist($account);
            }
        }

        $employee->setIsActive(false);
        $this->entityManager->persist($employee);
        $this->entityManager->flush();
    }

    public function reactivate(Employee $employee, string $username, string $password) {
        $account = new Account();
        $account->setEmployee($employee);
        $account->setName('EmpRec');
        $account->setUsername($username);
        $account->setPassword($password);
        $account->setValidTo(null);

        $this->entityManager->persist($account);

        $employee->setIsActive(true);
        $this->entityManager->persist($employee);

        $this->entityManager->flush();
        return $account;
    }

    public function delete(Employee $employee)
    {
        foreach ($employee->getAccounts() as $account)
            $this->entityManager->remove($account);

        $this->entityManager->remove($employee);
        $this->entityManager->flush();
    }
}
