<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;


class DashboardController extends AbstractController
{
    #[Route('/user-dashboard', name: 'app_user_dashboard')]
    public function userDashboardIndex(): Response
    {
        return $this->render('dashboard/user.html.twig', [
            'controller_name' => 'DashboardController',
        ]);
    }

    #[Route('/admin-dashboard', name: 'app_admin_dashboard')]
    public function adminDashboardIndex(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $sensorRepository = $entityManager->getRepository('App\Entity\Sensor');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $rooms = $roomRepository->findAll();
        $sensors = $sensorRepository->findAll();
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        return $this->render('dashboard/admin.html.twig', [
            'rooms' => $rooms,
            'sensors' => $sensors,
            'acquisitionSystems' => $acquisitionSystems,
            'controller_name' => 'DashboardController',
        ]);
    }
}
