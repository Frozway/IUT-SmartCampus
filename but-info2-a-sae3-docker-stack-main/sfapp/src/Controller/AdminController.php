<?php

namespace App\Controller;

use App\Entity\AcquisitionSystem;
use App\Entity\Department;
use App\Entity\Room;
use App\Form\AcquisitionSystemSelectionType;
use App\Form\AcquisitionSystemType;
use App\Form\DepartmentType;
use App\Form\RoomType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

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
    #[IsGranted("ROLE_ADMIN")]
    public function roomIndex(?int $id, ManagerRegistry $doctrine, Request $request, HttpClientInterface $httpClient): Response
    {
        $entityManager = $doctrine->getManager();
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');
        $roomRepository = $entityManager->getRepository('App\Entity\Room');

        $room = $roomRepository->find($id);

        if (!$room) {
            throw $this->createNotFoundException('La salle n\'existe pas');
        }

        $acquisitionSystem = $room->getAcquisitionSystem();
        $unassignedAcquisitionSystems = $acquisitionSystemRepository->findBy(['room' => null]);

        $form = $this->createForm(AcquisitionSystemSelectionType::class, null, [
            'unassigned_acquisition_systems' => $unassignedAcquisitionSystems,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $selectedAcquisitionSystem = $form->getData()['acquisitionSystem'];

            $room->setAcquisitionSystem($selectedAcquisitionSystem);

            if ($selectedAcquisitionSystem->isIsInstalled() == 1) {
                $selectedAcquisitionSystem->setState(0);
            } else {
                $selectedAcquisitionSystem->setState(1);
            }

            $entityManager->persist($room);
            $entityManager->flush();

            $this->addFlash('success', 'Le système d\'acquisition a été attribué à la salle avec succès. Le technicien en a été informé.');
            return $this->redirectToRoute('app_admin_room', ['id' => $id]);
        }

        $dataLimit = $request->query->get('dataLimit', 12);

        try {
            // Effectuer une requête HTTP à votre API
            $apiResponse = $httpClient->request('GET', "https://sae34.k8s.iut-larochelle.fr/api/captures/last?limit={$dataLimit}", [
                'headers' => [
                    'accept' => 'application/ld+json',
                    'dbname' => 'sae34bdk1eq3',
                    'username' => 'k1eq3',
                    'userpass' => 'wohtuh-nigzup-diwhE4',
                ],
            ]);
            $apiData = $apiResponse->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de récupérer les données de l\'API.');
            $apiData = [];
        }

        usort($apiData, function ($a, $b) {
            return strtotime($b['dateCapture']) - strtotime($a['dateCapture']); // Les valeurs sont triées par ordre décroissant (la plus récente en premier)
        });

        return $this->render('admin/room.html.twig', [
            'room' => $room,
            'acquisitionSystem' => $acquisitionSystem,
            'form' => $form->createView(),
            'data' => $apiData,
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
    #[IsGranted("ROLE_ADMIN")]
    public function addRoom(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $room = new Room();

        $form = $this->createForm(RoomType::class, $room);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($room);
            $entityManager->flush();

            $this->addFlash('success', 'La salle a été ajoutée avec succès.');

            return $this->redirectToRoute('app_admin_dashboard');
        }

        $errors = $validator->validate($room);

        return $this->render('admin/addRoom.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
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
    #[IsGranted("ROLE_ADMIN")]
    public function editRoom(Request $request, EntityManagerInterface $entityManager, ?int $id, ValidatorInterface $validator): Response
    {
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

            $this->addFlash('success', 'La salle a été modifiée avec succès.');

            return $this->redirectToRoute('app_admin_room', ['id' => $id]);
        }

        $errors = $validator->validate($room);

        return $this->render('admin/editRoom.html.twig', [
            'form' => $form->createView(),
            'room' => $id,
            'errors' => $errors,
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
    #[IsGranted("ROLE_ADMIN")]
    public function addAcquisitionSystemIndex(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $acquisitionSystem = new AcquisitionSystem();

        // Récupérer la liste des salles qui n'ont pas de système d'acquisition
        $unassociatedRooms = $entityManager
            ->getRepository('App\Entity\Room')
            ->findRoomsWithoutAcquisitionSystem();

        $form = $this->createForm(AcquisitionSystemType::class, $acquisitionSystem, [
            'unassociated_rooms' => $unassociatedRooms,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si une salle a été choisie
            if ($acquisitionSystem->getRoom() !== null) {
                // Demander au technicien d'installer le système d'acquisition
                $acquisitionSystem->setState(1);
            } else {
                // Ne rien faire si aucune salle n'a été choisie
                $acquisitionSystem->setState(0);
            }

            $entityManager->persist($acquisitionSystem);
            $entityManager->flush();

            $this->addFlash('success', 'Le système d\'acquisition a été ajouté avec succès.');

            return $this->redirectToRoute('app_admin_dashboard');
        }

        $errors = $validator->validate($acquisitionSystem);

        return $this->render('admin/addAcquisitionSystem.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
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
    #[IsGranted("ROLE_ADMIN")]
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

        // Si le système d'acquisition est installé, demander au technicien de le désinstaller, sinon il s'agissait d'une erreur donc on ne fait rien
        if ($acquisitionSystem->isIsInstalled() == 1) {
            $acquisitionSystem->setState(2);
        } else {
            $acquisitionSystem->setState(0);
        }

        // Supprimer la salle
        $entityManager->remove($room);
        $entityManager->flush();

        // Ajouter un message flash pour confirmer la suppression
        $this->addFlash('success', 'La salle a été supprimée avec succès.');

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
    #[IsGranted("ROLE_ADMIN")]
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

        // Si le système d'acquisition est installé, demander au technicien de le désinstaller, sinon il s'agissait d'une erreur donc on ne fait rien
        if ($acquisitionSystem->isIsInstalled() == 1) {
            $acquisitionSystem->setState(2);
        } else {
            $acquisitionSystem->setState(0);
        }

        $entityManager->flush();

        $this->addFlash('success', 'Le système d\'acquisition a été désattribué avec succès.');

        // Redirection une fois la suppression terminee
        return $this->redirectToRoute('app_admin_room', ['id' => $id]);
    }

    /**
     * @Route('/admin-dashboard/acquisition-system/{id}', name: 'app_admin_edit_acquisition_system')
     *
     * Details d'un système d'acquisition
     *
     * @param int $id L'identifiant du système d'acquisition
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @return RedirectResponse
     */
    #[Route('/admin-dashboard/acquisition-system/{id}', name: 'app_admin_edit_acquisition_system')]
    #[IsGranted("ROLE_ADMIN")]
    public function editAcquisitionSystem(Request $request, int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        // Récupérer le système d'acquisition par son ID
        $acquisitionSystem = $acquisitionSystemRepository->find($id);

        // Récupérer la liste des salles qui n'ont pas de système d'acquisition
        $unassociatedRooms = $entityManager
            ->getRepository('App\Entity\Room')
            ->findRoomsWithoutAcquisitionSystem();

        $assignedRoom = $acquisitionSystem->getRoom();

        if ($assignedRoom) {
            $unassociatedRooms = array_merge($unassociatedRooms, array($assignedRoom));
        }

        $form = $this->createForm(AcquisitionSystemType::class, $acquisitionSystem, [
            'unassociated_rooms' => $unassociatedRooms,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Vérifier si une salle a été choisie
            if ($acquisitionSystem->getRoom() !== null) {
                // Demander au technicien d'installer le système d'acquisition
                $acquisitionSystem->setState(1);
            } else {
                // Ne rien faire si aucune salle n'a été choisie
                $acquisitionSystem->setState(0);
            }

            $entityManager->flush();

            $this->addFlash('success', 'Le système d\'acquisition a été modifié avec succès.');

            return $this->redirectToRoute('app_admin_edit_acquisition_system', ['id' => $id]);
        }

        if (!$acquisitionSystem) {
            // Gérer le cas où le système d'acquisition n'est pas trouvé
            throw $this->createNotFoundException('Le système d\'acquisition n\'existe pas');
        }

        return $this->render('admin/acquisitionSystem.html.twig', [
            'acquisitionSystem' => $acquisitionSystem,
            'form' => $form->createView(),
            'form_errors' => $form->getErrors(true),
        ]);
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
    #[IsGranted("ROLE_ADMIN")]
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

        // Ajouter un message flash pour confirmer la suppression
        $this->addFlash('success', 'Le système d\'acquisition a été supprimé avec succès.');

        // Rediriger vers une autre page après la suppression
        return $this->redirectToRoute('app_admin_dashboard');
    }

    /**
     * @Route('/admin-dashboard/add-department', name: 'app_admin_add_department')
     *
     * Affiche le formulaire d'ajout d'un departement et l'ajoute à la base de données.
     *
     * @param Request $request La requête HTTP
     * @param EntityManagerInterface $entityManager L'entité de gestion
     * @return Response
     */
    #[Route('/admin-dashboard/add-department', name: 'app_admin_add_department')]
    #[IsGranted("ROLE_ADMIN")]
    public function addDepartment(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): Response
    {
        $department = new Department();

        $form = $this->createForm(DepartmentType::class, $department);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($department);
            $entityManager->flush();

            $this->addFlash('success', 'Le département a été ajouté avec succès.');

            return $this->redirectToRoute('app_admin_add_room');
        }

        $errors = $validator->validate($department);

        return $this->render('admin/addDepartment.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors,
        ]);
    }

}
