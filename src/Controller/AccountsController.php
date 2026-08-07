<?php

namespace App\Controller;

use App\Entity\Account;
use App\Form\AccountDeleteType;
use App\Form\AccountType;
use App\Service\AccountService;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

final class AccountsController extends AbstractController
{
    #[Route('/accounts/{id}', name: 'accounts', requirements: ['id' => '\d+'])]
    public function index(EmployeeService $employeeService, AccountService $accountService,
                          Request $request, int $id): Response
    {
        $employee = $employeeService->findById($id);

        if ($employee === null)
            throw new HttpException(404,'Not Found');

        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $data = $accountService->paginate($page, $limit, $employee);
        $accounts = $data['accounts'];
        $accountsCnt = $data['accountsCnt'];

        $pageCnt = max(1,  ceil($accountsCnt / $limit));

        if ($page > $pageCnt)
            throw new HttpException(400,"Bad Request");

        $deleteForm = $this->createForm(AccountDeleteType::class);

        return $this->render('account/accounts.html.twig', [
            'controller_name' => 'AccountsController',
            'employee' => $employee,
            'accounts' => $accounts,
            'page' => $page,
            'limit' => $limit,
            'pageCnt' => $pageCnt,
            'accountForm' => $deleteForm->createView(),
        ]);
    }

    #[Route('/account/create', name: 'account_create')]
    public function form(AccountService $accountService, Request $request): Response
    {
        $account = null;

        $form = $this->createForm(AccountType::class, $account);
        $form->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $account = $form->getData();
            if (!$account instanceof Account)
                throw new \LogicException( 'Invalid data received' );

            $accountService->save($account);
            return $this->redirectToRoute('accounts', [ 'id' => $account->getEmployee()->getId() ]);
        }

        return $this->render('account/form.html.twig', [
            'accountForm' => $form,
            'toDelete' => false,
        ]);
    }

    #[Route('/account/{id}/edit', name: 'account_edit', requirements: ['id' => '\d+'])]
    public function edit(): Response {
        throw new HttpException(501, "Editing account is not yet implemented");
    }

    #[Route('/account/{id}/delete', name: 'account_delete', requirements: ['id' => '\d+'])]
    public function delete(AccountService $accountService, ?int $id = null, Request $request): Response {
        $account = null;

        if ($id) {
            $account = $accountService->findById($id);
            if (!$account)
                throw new HttpException(404,"Not Found");
        }

        $form = $this->createForm(AccountDeleteType::class, $account);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $accountService->delete($account);
            return $this->redirectToRoute('accounts', [ 'id' => $account->getEmployee()->getId() ]);
        }

        return $this->render('account/form.html.twig', [
            'accountForm' => $form,
            'toDelete' => true,
        ]);
    }
}
