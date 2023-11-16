<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Form\AcquisitionSystemType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;
use App\Repository\SensorRepository;
use App\Repository\AcquisitionSystemRepository;
use App\Form\RoomType;
use App\Entity\Room;


class AdminController extends AbstractController
{
    #[Route('/admin-dashboard/room/{id?}', name: 'app_admin_room')]
    public function roomIndex(?int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $sensorRepository = $entityManager->getRepository('App\Entity\Sensor');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $room = $roomRepository->findOneBy(['id' => $id]);
        $acquisitionSystem = $acquisitionSystemRepository->findOneBy(['room' => $room]);
        $sensors = $sensorRepository->findBy(['acquisitionSystem' => $acquisitionSystem]);

        return $this->render('admin/room.html.twig', [
            'room' => $room,
            'sensors' => $sensors,
            'acquisitionSystem' => $acquisitionSystem,
            'controller_name' => 'AdminController',
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
    public function assignAcquisitionSystemIndex(): Response
    {
        return $this->render('admin/assignAcquisitionSystem.html.twig', [
            'controller_name' => 'AdminController',
        ]);
    }

    #[Route('/admin-dashboard/add-room', name: 'app_admin_add_room')]
    public function addRoom(Request $request, EntityManagerInterface $entityManager): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($room);
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addRoom.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AddRoom',
        ]);
    }

    #[Route('/admin-dashboard/add-acquisition-system', name: 'app_admin_add_acquisition_system')]
    public function addAcquisitionSystemIndex(Request $request, EntityManagerInterface $entityManager): Response
    {        
        $acquisitionSystem = new AcquisitionSystem();
        $form = $this->createForm(AcquisitionSystemType::class, $acquisitionSystem);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Effectuez ici ce que vous souhaitez avec les données du formulaire
            // Par exemple, persistez en base de données

            $entityManager->persist($acquisitionSystem);
            $entityManager->flush();

            // Redirigez l'utilisateur après la soumission du formulaire
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addAcquisitionSystem.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AddAcquisitionSystem',
        ]);
    }
}
