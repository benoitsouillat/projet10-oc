<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;


final class MainController extends AbstractController
{
    #[Route('', name: 'index')]
    public function index(Security $security): Response
    {
        if (!$security->getUser()) {
            return $this->redirectToRoute('app_login');
        }
        return $this->redirectToRoute('app_project_index');
    }
}
