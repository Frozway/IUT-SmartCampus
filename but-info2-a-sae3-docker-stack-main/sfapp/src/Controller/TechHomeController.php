<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechHomeController extends AbstractController
{
    #[Route('/home-tech', name: 'app_tech_home')]
    public function index(): Response
    {
        return $this->render('tech_home/index.html.twig', [
            'controller_name' => 'TechHomeController',
        ]);
    }
}
