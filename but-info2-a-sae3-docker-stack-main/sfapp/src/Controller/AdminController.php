<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Form\AcquisitionSystemType;
use App\Form\AcquisitionSystemSelectionType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\RoomRepository;
use App\Repository\AcquisitionSystemRepository;
use App\Form\RoomType;
use App\Entity\Room;
use Symfony\Component\HttpFoundation\RedirectResponse;

class AdminController extends AbstractController
{
    #[Route('/admin-dashboard/room/{id?}', name: 'app_admin_room')]
    public function roomIndex(?int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');

        // Récupérer la salle par son ID
        $room = $roomRepository->find($id);

        // Vérifier si la salle existe
        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas');
        }

        // Récupérer le système d'acquisition associé à la salle
        $acquisitionSystem = $room->getAcquisitionSystem();

        // Récupérer la liste des systèmes d'acquisition non assignés
        $unassignedAcquisitionSystems = $entityManager
            ->getRepository('App\Entity\AcquisitionSystem')
            ->findBy(['room' => null]);

        // Créer le formulaire de sélection du système d'acquisition avec la liste des systèmes non assignés
        $form = $this->createForm(AcquisitionSystemSelectionType::class, null, [
            'unassigned_acquisition_systems' => $unassignedAcquisitionSystems,
        ]);

        // Gérer la soumission du formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le système d'acquisition sélectionné
            $selectedAcquisitionSystem = $form->getData()['acquisitionSystem'];

            // Assigner le système d'acquisition à la salle
            $room->setAcquisitionSystem($selectedAcquisitionSystem);

            // Enregistrer les modifications dans la base de données
            $entityManager->persist($room);
            $entityManager->flush();

            // Redirection avec un message de succès
            $this->addFlash('success', 'Le système d\'acquisition a été attribué à la salle avec succès.');
            return $this->redirectToRoute('app_admin_room', ['id' => $id]);
        }

        return $this->render('admin/room.html.twig', [
            'room' => $room,
            'acquisitionSystem' => $acquisitionSystem,
            'controller_name' => 'AdminController',
            'form' => $form->createView(),
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

    #[Route('/admin-dashboard/add-acquisition-system', name: 'app_admin_add_acquisition_system')]
    public function addAcquisitionSystemIndex(Request $request, EntityManagerInterface $entityManager): Response
    {        
        $acquisitionSystem = new AcquisitionSystem();
        $form = $this->createForm(AcquisitionSystemType::class, $acquisitionSystem);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($acquisitionSystem);
            $entityManager->flush();

            // Redirection de l'utilisateur après la soumission du formulaire
            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addAcquisitionSystem.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AddAcquisitionSystem',
        ]);
    }

    #[Route('/admin-dashboard/room/{id}/delete', name: 'app_admin_delete_room')]
    public function deleteRoom(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');

        // Récupérer la salle par son ID
        $room = $roomRepository->find($id);

        if (!$room) {
            // Gérer le cas où la salle n'est pas trouvée
            throw $this->createNotFoundException('La salle n\'existe pas');
        }

        // Récupérer le système d'acquisition lié à la salle
        $acquisitionSystem = $room->getAcquisitionSystem();

        // Retirer la salle du système d'acquisition lié
        if ($acquisitionSystem) {
            $acquisitionSystem->setRoom(null);
            $room->setAcquisitionSystem(null);
        }

        // Supprimer la salle
        $entityManager->remove($room);
        $entityManager->flush();

        // Rediriger vers une autre page après la suppression
        return $this->redirectToRoute('app_admin_dashboard');
    }

    #[Route('/admin-dashboard/room/{id}/unassign-as', name: 'app_admin_unassign_as')]
    public function unassignAS(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $roomRepository = $entityManager->getRepository('App\Entity\Room');

        $room = $roomRepository->find($id);

        // Erreur si la salle n'existe pas
        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas');
        }

        // desatribuation du S.A.
        $acquisitionSystem = $room->getAcquisitionSystem();

        if ($acquisitionSystem) {
            $acquisitionSystem->setRoom(null);
            $room->setAcquisitionSystem(null);
        }

        $entityManager->flush();

        // Redirection une fois la suppression terminee
        return $this->redirectToRoute('app_admin_room', ['id'=> $id]);
    }

    #[Route('/admin-dashboard/acquisition-system/{id}/delete', name: 'app_admin_delete_acquisition_system')]
    public function deleteAcquisitionSystem(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        // Récupérer le système d'acquisition par son ID
        $acquisitionSystem = $acquisitionSystemRepository->find($id);

        if (!$acquisitionSystem) {
            // Gérer le cas où le système d'acquisition n'est pas trouvé
            throw $this->createNotFoundException('Le système d\'acquisition n\'existe pas');
        }

        // Définir la relation avec la salle sur null
        $acquisitionSystem->setRoom(null);


        // Supprimer le système d'acquisition
        $entityManager->remove($acquisitionSystem);
        $entityManager->flush();

        // Rediriger vers une autre page après la suppression
        return $this->redirectToRoute('app_admin_dashboard');
    }

}
