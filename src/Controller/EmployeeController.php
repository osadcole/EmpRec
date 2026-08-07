<?php

namespace App\Controller;

use App\Entity\Employee;
use App\Form\EmployeeDeleteType;
use App\Form\EmployeeSearchType;
use App\Form\EmployeeType;
use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

final class EmployeeController extends AbstractController
{
    #[Route('/employees', name: 'employees_index')]
    public function index(EmployeeService $employeeService, Request $request): Response
    {
        $page = $request->query->getInt('page', 1);
        $limit = $request->query->getInt('limit', 10);

        $form = $this->createForm(EmployeeSearchType::class, null, [ 'method' => 'GET' ]);
        $form->handleRequest($request);

        $employeeService->processFormData($form->getData());

        $data = $employeeService->search($page, $limit);
        $employees = $data['employees'];
        $employeesCnt = $data['employeesCnt'];

        $pageCnt = max(1,  ceil($employeesCnt / $limit));
        if ($page > $pageCnt)
            throw new HttpException(400,"Bad Request");

        return $this->render('employee/employees.html.twig', [
            'employees' => $employees,
            'page' => $page,
            'limit' => $limit,
            'pageCnt' => $pageCnt,
            'searchForm' => $form,
        ]);
    }

    #[Route('/employee/{id}', name: 'employee_show', requirements: ['id' => '\d+'])]
    public function show(EmployeeService $employeeService, int $id): Response
    {
        $employee = $employeeService->findById($id);
        if ($employee === null)
            throw new HttpException(404,'Not Found');

        return $this->render('employee/profile.html.twig', [
            'controller_name' => 'EmployeeController',
            'employee' => $employee,
        ]);
    }

    #[Route('/employee/create', name: 'employee_create')]
    #[Route('/employee/{id}/edit', name: 'employee_edit', requirements: ['id' => '\d+'])]
    public function form(EmployeeService $employeeService, ?int $id = null, Request $request,
                    #[Autowire('%kernel.project_dir%/public/uploads/images')] string $imagesDirectory
    ): Response
    {
        $employee = null;

        if ($id) {
            $employee = $employeeService->findById($id);
            if (!$employee)
                throw new HttpException(404,"Not Found");
        }

        $form = $this->createForm(EmployeeType::class, $employee);
        $form->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employee = $form->getData();
            if (!$employee instanceof Employee)
                throw new \LogicException( 'Invalid data received' );

            $imageFile = $form->get('photo')->getData();

            $employeeService->save($employee, $imageFile, $imagesDirectory);
            return $this->redirectToRoute('employee_show', [ 'id' => $employee->getId() ]);
        }

        return $this->render('employee/edit.html.twig', [
            'employeeForm' => $form,
        ]);
    }

    #[Route('/employee_search_form', name: 'employee_search_form')]
    public function searchFormFragment(): Response
    {
        $form = $this->createForm(EmployeeSearchType::class, null, [
            'method' => 'GET',
            'action' => $this->generateUrl('employees_index'),
        ]);
        $form->add('Search', SubmitType::class);


        return $this->render('employee/search.html.twig', [
            'searchForm' => $form->createView(),
        ]);
    }

    #[\Symfony\Component\Routing\Annotation\Route('/employee/{id}/deactivate', name: 'employee_deactivate', requirements: ['id' => '\d+'])]
    public function deactivate(EmployeeService $employeeService, int $id): Response
    {
        $employee = $employeeService->findById($id);
        if ($employee === null)
            throw new HttpException(404,'Not Found');

        $employeeService->deactivate($employee);
        return $this->redirectToRoute('employee_show', ['id' => $id]);
    }

    #[\Symfony\Component\Routing\Annotation\Route('/employee/{id}/reactivate', name: 'employee_reactivate', requirements: ['id' => '\d+'])]
    public function reactivate(EmployeeService $employeeService, int $id): Response
    {
        $employee = $employeeService->findById($id);
        if ($employee === null)
            throw new HttpException(404,'Not Found');

        $username = strtolower($employee->getSurname().'_'.$employee->getName().uniqid());
        $password = strtolower($employee->getSurname().'_'.$employee->getName().uniqid());

        $account = $employeeService->reactivate($employee, $username, $password);
        return $this->redirectToRoute('employee_show', ['id' => $id]);
    }

    #[Route('/employee/{id}/delete', name: 'employee_delete', requirements: ['id' => '\d+'])]
    public function delete(EmployeeService $employeeService, ?int $id = null, Request $request): Response {
        $employee = null;

        if ($id) {
            $employee = $employeeService->findById($id);
            if (!$employee)
                throw new HttpException(404,"Not Found");
        }

        $form = $this->createForm(EmployeeDeleteType::class, $employee);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $employeeService->delete($employee);
            return $this->redirectToRoute('employees_index');
        }

        return $this->render('employee/delete.html.twig', [
            'employeeForm' => $form,
            'toDelete' => true,
        ]);
    }
}
