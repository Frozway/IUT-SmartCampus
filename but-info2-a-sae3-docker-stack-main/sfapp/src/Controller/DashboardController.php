<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/user-dashboard', name: 'app_user_dashboard')]
    public function userDashboardIndex(): Response
    {
        return $this->render('dashboard/userIndex.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/admin-dashboard', name: 'app_admin_dashboard')]
    public function adminDashboardIndex(): Response
    {
        return $this->render('dashboard/adminIndex.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }
}
