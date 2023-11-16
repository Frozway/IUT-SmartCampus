<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;
use App\Repository\SensorRepository;
use App\Repository\AcquisitionSystemRepository;

class AdminController extends AbstractController
{
    #[Route('/admin-dashboard/room/{name?}', name: 'app_admin_room')]
    public function roomIndex(?string $name, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $sensorRepository = $entityManager->getRepository('App\Entity\Sensor');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $room = $roomRepository->findOneBy(['name' => $name]);
        $acquisitionSystem = $acquisitionSystemRepository->findOneBy(['room' => $room]);
        $sensors = $sensorRepository->findBy(['acquisitionSystem' => $acquisitionSystem]);

        return $this->render('admin/room.html.twig', [
            'room' => $room,
            'sensors' => $sensors,
            'acquisitionSystem' => $acquisitionSystem,
        ]);
    }

    #[Route('/admin-dashboard/assign-sensor', name: 'app_admin_assign_sensor')]
    public function sensorIndex(): Response
    {
        return $this->render('admin/sensor.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin-dashboard/assign-acquisition-system', name: 'app_admin_assign_acquisition_system')]
    public function acquisitionSystemIndex(): Response
    {
        return $this->render('admin/acquisitionSystem.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }
}
