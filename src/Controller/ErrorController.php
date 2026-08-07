<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\ErrorHandler\Exception\FlattenException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ErrorController extends AbstractController
{
    #[Route('/error', name: 'error')]
    public function show(FlattenException $exception): Response
    {
        return $this->render('bundles/TwigBundle/Exception/error.html.twig', [
            'controller_name' => 'ErrorController',
            'error' => [
                'code' => $exception->getStatusCode(),
                'message' => $exception->getMessage(),
            ],
        ]);
    }
}
