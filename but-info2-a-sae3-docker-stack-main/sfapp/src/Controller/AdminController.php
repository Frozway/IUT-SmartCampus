<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Form\RoomType;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Room;


class AdminController extends AbstractController
{
    #[Route('/admin-dashboard/room', name: 'app_admin_room')]
    public function roomIndex(): Response
    {
        return $this->render('admin/room.html.twig', [
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
    public function acquisitionSystemIndex(): Response
    {
        return $this->render('admin/acquisitionSystem.html.twig', [
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
        ]);
    }

    #[Route('/admin-dashboard/edit-room/{id?}', name: 'app_admin_edit_room')]
    public function editRoom(Request $request, EntityManagerInterface $entityManager, ?int $id) : Response {
        if (is_null($id)) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        $room = $entityManager->find(Room::class, $id);

        if (is_null($room)) {
            return $this->redirectToRoute('app_admin_dashboard');
        }

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/editRoom.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
