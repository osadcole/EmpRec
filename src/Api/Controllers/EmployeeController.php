<?php

namespace App\Api\Controllers;

use App\Api\Dto\EmployeeInput;
use App\Api\Dto\EmployeeOutput;
use App\Api\Dto\RolesAddInput;
use App\Entity\Employee;
use App\Service\EmployeeService;
use App\Service\RoleService;
use App\Utils\Validation;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;

#[Route(path: '/api/employee', name: 'api_employee_')]
class EmployeeController extends AbstractFOSRestController
{
    public function __construct(
        private EmployeeService $employeeService,
        private RoleService $roleService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(path: '', name: 'list', methods: ['GET'])]
    public function list(Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $query = $request->query->all();
        $searchQuery = $query['employee_search'] ?? [];
        $this->employeeService->searchApi($searchQuery, $this->roleService);

        $data = $this->employeeService->search($page, $limit);
        $employees = $data['employees'];
        $employeesCnt = $data['employeesCnt'];

        $employeesOutput = array_map(fn (Employee $employee) => EmployeeOutput::fromEntity($employee), $employees);

        return $this->handleView($this->view([
            'employees' => $employeesOutput,
            'page' => $page,
            'limit' => $limit,
            'pageCnt' => max(1, ceil($employeesCnt / $limit)),
        ], Response::HTTP_OK));
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => '\d+'], methods: ['GET'])]
    public function show(EmployeeService $employeeService, int $id): Response
    {
        $employee = $employeeService->findById($id);
        if (!$employee)
            return $this->handleView($this->view(['message' => 'Not found'], Response::HTTP_NOT_FOUND));

        return $this->handleView($this->view(EmployeeOutput::fromEntity($employee), Response::HTTP_OK));
    }

    #[Route(path: '', name: 'create', methods: ['POST'])]
    #[Route(path: '/{id}', name: 'update', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function update(Request $request, ?int $id = null): Response
    {
        $employee = null;
        if ($id !== null) {
            $employee = $this->employeeService->findById($id);
            if (!$employee)
                return $this->handleView($this->view(['message' => 'Not found'], Response::HTTP_NOT_FOUND));
        }

        $format = $request->getContentTypeFormat();
        $inputEmployee = $this->serializer->deserialize($request->getContent(), EmployeeInput::class, $format);

        $employee = $inputEmployee->toEntity($this->roleService, $employee);

        $errors = Validation::validateObject($this->validator, $employee);
        if ($errors !== null)
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));

        $this->employeeService->save($employee);

        return $this->handleView($this->view(EmployeeOutput::fromEntity($employee),
            $id === null ? Response::HTTP_CREATED : Response::HTTP_OK));
    }

    #[Route(path: '/{id}/role', name: 'add_role', requirements: ['id' => '\d+'], methods: ['PUT'])]
    public function addRole(Request $request, int $id): Response
    {
        $employee = $this->employeeService->findById($id);
        if (!$employee)
            return $this->handleView($this->view(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND));

        $format = $request->getContentTypeFormat();
        $inputRoles = $this->serializer->deserialize($request->getContent(), RolesAddInput::class, $format);

        foreach ($inputRoles->roleIds as $roleId) {
            $role = $this->roleService->findById($roleId);
            if ($role)
                $employee->addRole($role);
        }

        $this->employeeService->save($employee);

        return $this->handleView($this->view( EmployeeOutput::fromEntity($employee), Response::HTTP_OK));
    }

    #[Route(path: '/{id}/role/{roleId}', name: 'remove_role', requirements: ['id' => '\d+', 'roleId' => '\d+'], methods: ['DELETE'])]
    public function removeRole(int $id, int $roleId): Response
    {
        $employee = $this->employeeService->findById($id);
        if (!$employee)
            return $this->handleView($this->view(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND));

        $role = $this->roleService->findById($roleId);
        if (!$role)
            return $this->handleView($this->view(['message' => 'Role not found'], Response::HTTP_NOT_FOUND));

        $employee->removeRole($role);
        $this->employeeService->save($employee);

        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }
}
