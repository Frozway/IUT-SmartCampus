<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin-dashboard/room', name: 'app_admin_room')]
    public function roomIndex(): Response
    {
        return $this->render('admin/indexRoom.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin-dashboard/assign-sensor', name: 'app_admin_assign_sensor')]
    public function sensorIndex(): Response
    {
        return $this->render('admin/indexSensor.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin-dashboard/assign-aquisition-system', name: 'app_admin_assign_aquisition_system')]
    public function aquisitionSystemIndex(): Response
    {
        return $this->render('admin/indexAquisitionSystem.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
