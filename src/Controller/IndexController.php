<?php

namespace App\Controller;

use App\Service\EmployeeService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class IndexController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(EmployeeService $employeeService): Response
    {
        $employees = $employeeService->findNewest();
        return $this->render('index.html.twig', [
            'controller_name' => 'IndexController',
            'employees' => $employees,
        ]);
    }
}
