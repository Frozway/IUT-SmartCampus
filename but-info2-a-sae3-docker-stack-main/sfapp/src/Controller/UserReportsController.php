<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserReportsController extends AbstractController
{
    #[Route('/user-reports', name: 'app_user_reports')]
    public function index(): Response
    {
        return $this->render('user_reports/index.html.twig', [
            'controller_name' => 'UserReportsController',
        ]);
    }
}
