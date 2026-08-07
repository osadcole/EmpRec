<?php

namespace App\Controller;

use App\Entity\Role;
use App\Form\RoleType;
use App\Service\RoleService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Attribute\Route;

final class RoleController extends AbstractController
{
    #[Route('/role/create', name: 'role_create')]
    #[Route('/role/{id}/edit', name: 'role_edit', requirements: ['id' => '\d+'])]
    public function form(RoleService $roleService, ?int $id = null, Request $request): Response
    {
        $role = null;

        if ($id) {
            $role = $roleService->findById($id);
            if (!$role)
                throw new HttpException(404,"Not Found");
        }

        $form = $this->createForm(RoleType::class, $role);
        $form->add('save', SubmitType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->getData();
            if (!$role instanceof Role)
                throw new \LogicException( 'Invalid data received' );

            $roleService->save($role);
            return $this->redirectToRoute('employees_index');
        }

        return $this->render('role/edit.html.twig', [
            'roleForm' => $form,
        ]);
    }
}
