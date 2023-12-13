<?php

namespace App\Controller;

use App\Form\FilterRoomDashboardType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
    public function adminDashboardIndex(ManagerRegistry $doctrine,Request $request): Response
    {
        $entityManager = $doctrine->getManager();

        $roomRepository = $entityManager->getRepository('App\Entity\Room');
        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        $rooms = $roomRepository->findAll();
        $acquisitionSystems = $acquisitionSystemRepository->findAll();

        $alerts = array();

        foreach ($acquisitionSystems as $as) {
            if ($as->isIsInstalled()) {
                // The alert is created if the value is NEAR the limit

                // CO2 too high
                if ($as->getCo2() > 1300) {
                    $alerts[] = array(
                        'type' => 'co2',
                        'category' => ($as->getCo2() > 1500) ? 'red' : 'orange',
                        'value' => $as->getCo2() . ' ppm',
                        'room' => $as->getRoom()->getName()
                    );
                }

                // Temperature to low or too high
                if ($as->getTemperature() > 21 || $as->getTemperature() < 18) {
                    $alerts[] = array(
                        'type' => 'temperature',
                        'category' => ($as->getTemperature() < 17) ? 'red' : 'orange',
                        'value' => $as->getTemperature() . '°C',
                        'room' => $as->getRoom()->getName()
                    );
                }
                
                // Humidity AND temperature too high
                if ($as->getHumidity() > 60 && $as->getTemperature() > 20) {
                    $alerts[] = array(
                        'type' => 'humidity',
                        'category' => ($as->getHumidity() > 70) ? 'red' : 'orange',
                        'value' => $as->getHumidity() . '%',
                        'room' => $as->getRoom()->getName()
                    );
                }
            }
        }

        $form = $this->createForm(FilterRoomDashboardType::class, $rooms);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
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
                'alerts' => $alerts   
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
            'alerts' => $alerts
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

        return $this->render('dashboard/tech.html.twig', [
            'rooms' => $rooms,
            'acquisitionSystems' => $acquisitionSystems,
        ]);

    }

}