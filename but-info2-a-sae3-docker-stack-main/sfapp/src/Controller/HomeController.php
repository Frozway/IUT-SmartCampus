<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/home-user', name: 'app_user_home')]
    public function indexUser(): Response
    {
        return $this->render('user_home/index.html.twig', [
            'controller_name' => 'UserHomeController',
        ]);
    }

    #[Route('/home-tech', name: 'app_tech_home')]
    public function indexTech(): Response
    {
        return $this->render('tech_home/index.html.twig', [
            'controller_name' => 'TechHomeController',
        ]);
    }
}
