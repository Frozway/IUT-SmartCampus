<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechController extends AbstractController
{
    #[Route('/tech', name: 'app_tech')]
    public function index(): Response
    {
        return $this->render('tech/index.html.twig', [
            'controller_name' => 'TechController',
        ]);
    }
}
