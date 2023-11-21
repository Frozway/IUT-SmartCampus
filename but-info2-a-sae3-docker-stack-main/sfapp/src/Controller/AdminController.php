<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Form\AcquisitionSystemType;
use App\Form\AcquisitionSystemSelectionType;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
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
    /**
     * @Route('/admin-dashboard/room/{id?}', name: 'app_admin_room')
     *
     * Affiche le détail d'une salle, permet d'attribuer/désattribuer un système d'acquisition.
     *
     * @param int|null $id L'identifiant de la salle
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @param Request $request La requête HTTP
     * @return Response
     */
    #[Route('/admin-dashboard/room/{id?}', name: 'app_admin_room')]
    public function roomIndex(?int $id, ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');

        $room = $roomRepository->find($id);

        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas');
        }

        $acquisitionSystem = $room->getAcquisitionSystem();

        // Récupérer la liste des systèmes d'acquisition non assignés
        $unassignedAcquisitionSystems = $entityManager
            ->getRepository('App\Entity\AcquisitionSystem')
            ->findBy(['room' => null]);

        // Créer le formulaire de sélection du système d'acquisition avec la liste des systèmes non assignés
        $form = $this->createForm(AcquisitionSystemSelectionType::class, null, [
            'unassigned_acquisition_systems' => $unassignedAcquisitionSystems,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Récupérer le système d'acquisition sélectionné
            $selectedAcquisitionSystem = $form->getData()['acquisitionSystem'];

            $room->setAcquisitionSystem($selectedAcquisitionSystem);

            $entityManager->persist($room);
            $entityManager->flush();

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
    /**
     * @Route('/admin-dashboard/add-room', name: 'app_admin_add_room')
     *
     * Affiche le formulaire d'ajout d'une salle et l'ajoute à la base de données.
     *
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $entityManager L'entité de gestion
     * @return Response
     */
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
    /**
     * @Route('/admin-dashboard/edit-room/{id?}', name: 'app_admin_edit_room')
     *
     * Affiche et traite le formulaire d'édition d'une salle.
     *
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $entityManager L'entité de gestion
     * @param int|null $id L'identifiant de la salle à éditer
     * @return Response
     */
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
            'room' => $id,
        ]);
    }
    /**
     * @Route('/admin-dashboard/add-acquisition-system', name: 'app_admin_add_acquisition_system')
     *
     * Affiche et traite le formulaire d'ajout d'un système d'acquisition.
     *
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $entityManager L'entité de gestion
     * @return Response
     */
    #[Route('/admin-dashboard/add-acquisition-system/{error}', name: 'app_admin_add_acquisition_system')]
    public function addAcquisitionSystemIndex(Request $request, EntityManagerInterface $entityManager, $error): Response
    {        
        $acquisitionSystem = new AcquisitionSystem();
        $form = $this->createForm(AcquisitionSystemType::class, $acquisitionSystem);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $entityManager->persist($acquisitionSystem);
                $entityManager->flush();
            } catch (UniqueConstraintViolationException $e) {
                return $this->redirectToRoute('app_admin_add_acquisition_system', [
                    'error' => '1',
                ]);
            }

            return $this->redirectToRoute('app_admin_dashboard');
        }

        return $this->render('admin/addAcquisitionSystem.html.twig', [
            'form' => $form->createView(),
            'controller_name' => 'AddAcquisitionSystem',
            'display_error' => $error
        ]);
    }
    /**
     * @Route('/admin-dashboard/add-acquisition-system', name: 'app_admin_add_acquisition_system')
     *
     * Affiche et traite le formulaire d'ajout d'un système d'acquisition.
     *
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $entityManager L'entité de gestion
     * @return Response
     */
    #[Route('/admin-dashboard/error', name: 'app_unique_constraint_error')]
    public function uniqueConstraintErrorIndex(Request $request, EntityManagerInterface $entityManager): Response
    {        

        return $this->render('admin/uniqueConstraintError.html.twig', [
            'controller_name' => 'uniqueConstraintError',
        ]);
        
    }
    /**
     * @Route('/admin-dashboard/room/{id}/delete', name: 'app_admin_delete_room')
     *
     * Supprime une salle, dissociant le système d'acquisition associé.
     *
     * @param int $id L'identifiant de la salle à supprimer
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @return RedirectResponse
     */
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
    /**
     * @Route('/admin-dashboard/room/{id}/unassign-as', name: 'app_admin_unassign_as')
     *
     * Désattribue le système d'acquisition d'une salle.
     *
     * @param int $id L'identifiant de la salle
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @return RedirectResponse
     */
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
    /**
     * @Route('/admin-dashboard/acquisition-system/{id}/delete', name: 'app_admin_delete_acquisition_system')
     *
     * Supprime un système d'acquisition, dissociant toute salle associée.
     *
     * @param int $id L'identifiant du système d'acquisition à supprimer
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @return RedirectResponse
     */
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
