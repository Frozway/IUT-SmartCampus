<?php

namespace App\Controller;

use App\Entity\Room;
use App\Entity\TechNotification;
use App\Form\FilterRoomDashboardType;
use App\Form\NotificationType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord de l'utilisateur.
     *
     * @Route('/user-dashboard', name='app_user_dashboard')
     * @return Response
     */
    #[Route('/', name: 'app_user_dashboard')]
    public function userDashboardIndex(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $rooms = $roomRepository->findAll();

        $form = $this->createForm(FilterRoomDashboardType::class, $rooms);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $floor = $form->get('Floor')->getData();

            $assigned = $form->get('isAssigned')->getData();

            $searchR = strtoupper($form->get('SearchRoom')->getData());

            $searchAS = strtoupper($form->get('SearchAS')->getData());

            return $this->render('dashboard/user.html.twig', [
                'rooms' => $rooms,
                'floor' => $floor,
                'assigned' => $assigned,
                'searchR' => $searchR,
                'searchAS' => $searchAS,
                'form' => $form,
            ]);
        }

        return $this->render('dashboard/user.html.twig', [
            'rooms' => $rooms,
            'floor' => null,
            'assigned' => null,
            'searchR' => null,
            'searchAS' => null,
            'form' => $form,
        ]);
    }

    /**
     * @Route('/user-dashboard/room/{id?}', name: 'app_admin_room')
     *
     * Affiche le détail d'une salle pour un utilisateur non connecté
     *
     * @param int|null $id L'identifiant de la salle
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @param Request $request La requête HTTP
     * @return Response
     */
    #[Route('/room/{id?}', name: 'app_room')]
    public function roomIndex(?int $id, ManagerRegistry $doctrine, Request $request, HttpClientInterface $httpClient): Response
    {
        $entityManager = $doctrine->getManager();
        $roomRepository = $entityManager->getRepository('App\Entity\Room');

        $room = $roomRepository->find($id);
        $rooms = $roomRepository->findAll();
        $acquisitionSystem = $room->getAcquisitionSystem();

        $dataLimit = $request->query->get('dataLimit', 864);

        // récuperation du fichier database.json
        $json = file_get_contents('json/database.json');
        $json_data = json_decode($json, true);
        try {
            $dbname = $json_data[$room->getName()]['dbname'];
        } catch (\Exception $e) {
            $dbname = null;
        }

        try {
            // Effectuer une requête HTTP à votre API
            $apiResponse = $httpClient->request('GET', "https://sae34.k8s.iut-larochelle.fr/api/captures/last?limit={$dataLimit}", [
                'headers' => [
                    'accept' => 'application/ld+json',
                    'dbname' => $dbname,
                    'username' => 'k1eq3',
                    'userpass' => 'wohtuh-nigzup-diwhE4',
                ],
            ]);

            $apiData = $apiResponse->toArray();
        } catch (\Exception $e) {
            $this->addFlash('error', 'Impossible de récupérer les données de l\'API.');
            $apiData = [];
        }

        // Les valeurs sont triées par ordre chronologique inverse (la plus récente en premier)
        usort($apiData, function ($a, $b) {
            return strtotime($b['dateCapture']) - strtotime($a['dateCapture']); 
        });

        $form = $this->createForm(FilterRoomDashboardType::class, $rooms);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {            
            return $this->redirectToRoute('app_room', [
                'id' => $id,
                'floor' => $form->get('Floor')->getData(),
                'assigned' => $form->get('isAssigned')->getData(),
                'name' => $form->get('SearchRoom')->getData(),
                'as' => $form->get('SearchAS')->getData(),
            ]);
        }     

        return $this->render('dashboard/user.html.twig', [
            'room' => $room,
            'rooms' => $rooms,
            'floor' => $request->get('floor'),
            'assigned' => $request->get('assigned'),
            'searchR' => $request->get('name'),
            'searchAS' => $request->get('as'),
            'form' => $form,
            'data' => $apiData,
            'acquisitionSystem' => $acquisitionSystem,
        ]);
    }

    #[Route('/submit-notification', name: 'app_submit_tech_notification')]
    public function submintNotificationIndex(ManagerRegistry $doctrine, Request $request, EntityManagerInterface $entityManager): Response
    {

        $notification = new TechNotification();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $rooms = $roomRepository->findAll();

        $form = $this->createForm(NotificationType::class, $notification, ['rooms' => $rooms]);

        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $notification->setCreationDate(new \DateTime("now", new \DateTimeZone("CET")));

            $entityManager->persist($notification);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_dashboard');

        }

        return $this->render('user/submitTechNotification.html.twig', [
            'controller_name' => 'DashboardController',
            'error' => null,
            'form' => $form->createView(),
        ]);
    }

    /**
     * Affiche le tableau de bord de l'administrateur.
     *
     * @Route('/admin-dashboard', name='app_admin_dashboard')
     * @param ManagerRegistry $doctrine
     * @return Response
     */
    #[Route('/admin-dashboard', name: 'app_admin_dashboard')]
    #[IsGranted("ROLE_ADMIN")]
    public function adminDashboardIndex(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $rooms = $roomRepository->findAll();
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        $form = $this->createForm(FilterRoomDashboardType::class, $rooms);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            return $this->redirectToRoute('app_admin_dashboard', [
                'floor' => $form->get('Floor')->getData(),
                'assigned' => $form->get('isAssigned')->getData() == '0' ? null : true,
                'name' => $form->get('SearchRoom')->getData(),
                'as' => $form->get('SearchAS')->getData(),
            ]);
        }

        return $this->render('dashboard/admin.html.twig', [
            'rooms' => $rooms,
            'acquisitionSystems' => $acquisitionSystems,
            'floor' => $request->get('floor'),
            'assigned' => $request->get('assigned'),
            'searchR' => $request->get('name'),
            'searchAS' => $request->get('as'),
            'form' => $form,
        ]);
    }

    /**
     * Affiche le tableau de bord du technicien.
     *
     * @Route('/tech-dashboard', name='app_tech_dashboard')
     * @return Response
     */
    #[Route('/tech-dashboard', name: 'app_tech_dashboard')]
    #[IsGranted("ROLE_TECHNICIAN")]
    public function techDashboardIndex(ManagerRegistry $doctrine, Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $rooms = $roomRepository->findAll();
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        $notificationsRepository = $entityManager->getRepository('App\Entity\TechNotification');
        $notifications = $notificationsRepository->findAll();

        usort($notifications, function ($a, $b) {
            if ($a->isIsRead() != $b->isIsRead()) {
                return $a->isIsRead() - $b->isIsRead(); // La distinction entre lu et non lu est prioritaire
            } else {
                if ($a->getCreationDate() == $b->getCreationDate()) {
                    return 0;
                }
                return $a->getCreationDate() < $b->getCreationDate() ? 1 : -1; // Tri du plus recent au plus ancien
            }
        });

        $alerts = array();

        foreach ($acquisitionSystems as $as) {
            if ($as->getRoom()) {
                if ($as->isIsInstalled()) {
                    // The alert is created if the value is NEAR the limit

                    // CO2 too high
                    if ($as->getCo2() > 1300) {
                        $alerts[] = array(
                            'category' => ($as->getCo2() > 1500) ? 'red' : 'orange',
                            'room' => $as->getRoom()->getName(),
                        );
                        continue;
                    }

                    // Temperature to low or too high
                    if ($as->getTemperature() > 21 || $as->getTemperature() < 18) {
                        $alerts[] = array(
                            'category' => ($as->getTemperature() < 17) ? 'red' : 'orange',
                            'room' => $as->getRoom()->getName(),
                        );
                        continue;
                    }

                    // Humidity AND temperature too high
                    if ($as->getHumidity() > 60 && $as->getTemperature() > 20) {
                        $alerts[] = array(
                            'category' => ($as->getHumidity() > 70) ? 'red' : 'orange',
                            'room' => $as->getRoom()->getName(),
                        );
                        continue;
                    }
                }
            }
        }

        return $this->render('dashboard/tech.html.twig', [
            'rooms' => $rooms,
            'acquisitionSystems' => $acquisitionSystems,
            'alerts' => $alerts,
            'notifications' => $notifications,
        ]);
    }

    #[Route('/notification-read/{id}', name: 'app_notification_read')]
    public function notificationRead(Request $request, int $id, ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $notificationsRepository = $entityManager->getRepository('App\Entity\TechNotification');
        $notification = $notificationsRepository->find($id);

        $notification->setIsRead(true);
        $entityManager->flush();

        return $this->redirectToRoute('app_tech_dashboard');
    }
}
