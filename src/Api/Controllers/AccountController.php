<?php

namespace App\Api\Controllers;

use App\Api\Dto\AccountInput;
use App\Api\Dto\AccountOutput;
use App\Service\AccountService;
use App\Service\EmployeeService;
use App\Utils\Validation;
use FOS\RestBundle\Controller\AbstractFOSRestController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use JMS\Serializer\SerializerInterface;

#[Route(path: '/api/account', name: 'api_account_')]
class AccountController extends AbstractFOSRestController
{
    public function __construct(
        private AccountService $accountService,
        private EmployeeService $employeeService,
        private SerializerInterface $serializer,
        private ValidatorInterface $validator
    ) {}

    #[Route(path: '', name: 'create', methods: ['POST'])]
    public function create(Request $request): Response
    {
        $format = $request->getContentTypeFormat();
        $inputAccount = $this->serializer->deserialize($request->getContent(), AccountInput::class, $format);

        $employee = $this->employeeService->findById($inputAccount->employeeId);
        if (!$employee)
            return $this->handleView($this->view(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND));

        $account = $inputAccount->toEntity($employee);

        $errors = Validation::validateObject($this->validator, $account);
        if ($errors !== null)
            return $this->handleView($this->view($errors, Response::HTTP_BAD_REQUEST));

        $this->accountService->save($account);
        return $this->handleView($this->view(AccountOutput::fromEntity($account), Response::HTTP_CREATED));
    }

    #[Route(path: '/employee/{employeeId}', name: 'list_employee_accounts', requirements: ['employeeId' => '\d+'], methods: ['GET'])]
    public function list(int $employeeId, Request $request): Response
    {
        $employee = $this->employeeService->findById($employeeId);
        if (!$employee)
            return $this->handleView($this->view(['message' => 'Employee not found'], Response::HTTP_NOT_FOUND));

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $data = $this->accountService->paginate($page, $limit, $employee);
        $accounts = $data['accounts'];
        $accountsCnt = $data['accountsCnt'];

        $accountsOutput = array_map(fn($account) => AccountOutput::fromEntity($account), $accounts);

        return $this->handleView($this->view([
            'accounts' => $accountsOutput,
            'page' => $page,
            'limit' => $limit,
            'pageCnt' => max(1, ceil($accountsCnt / $limit)),
        ], Response::HTTP_OK));
    }

    #[Route(path: '/{id}', name: 'delete', requirements: ['id' => '\d+'], methods: ['DELETE'])]
    public function delete(int $id): Response
    {
        $account = $this->accountService->findById($id);
        if (!$account)
            return $this->handleView($this->view(['message' => 'Account not found'], Response::HTTP_NOT_FOUND));

        $this->accountService->delete($account);
        return $this->handleView($this->view(null, Response::HTTP_NO_CONTENT));
    }
}
