<?php

namespace App\Controller;

use App\Form\FilterRoomDashboardType;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class DashboardController extends AbstractController
{
    /**
     * Affiche le tableau de bord de l'utilisateur.
     *
     * @Route('/user-dashboard', name='app_user_dashboard')
     * @return Response
     */
    #[Route('/user-dashboard', name: 'app_user_dashboard')]
    public function userDashboardIndex(): Response
    {
        return $this->render('dashboard/user.html.twig', [
            'controller_name' => 'DashboardController',
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

                    // Temperature too low or too high
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

        $form = $this->createForm(FilterRoomDashboardType::class, $rooms);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $floor = $form->get('Floor');
            $floor = $floor->getData();

            $assigned = $form->get('isAssigned');
            $assigned = $assigned->getData();

            $searchR = $form->get('SearchRoom');
            $searchR = $searchR->getData();
            $searchR = strtoupper($searchR);

            $searchAS = $form->get('SearchAS');
            $searchAS = $searchAS->getData();
            $searchAS = strtoupper($searchAS);

            return $this->render('dashboard/admin.html.twig', [
                'rooms' => $rooms,
                'acquisitionSystems' => $acquisitionSystems,
                'floor' => $floor,
                'assigned' => $assigned,
                'searchR' => $searchR,
                'searchAS' => $searchAS,
                'form' => $form,
                'alerts' => $alerts,
            ]);
        }

        return $this->render('dashboard/admin.html.twig', [
            'rooms' => $rooms,
            'acquisitionSystems' => $acquisitionSystems,
            'floor' => null,
            'assigned' => null,
            'searchR' => null,
            'searchAS' => null,
            'form' => $form,
            'alerts' => $alerts,
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
            }
            else
            {
                if ($a->getCreationDate() == $b->getCreationDate())
                {
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
