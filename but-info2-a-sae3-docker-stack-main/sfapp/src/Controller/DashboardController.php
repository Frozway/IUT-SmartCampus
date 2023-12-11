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

        $form = $this->createForm(FilterRoomDashboardType::class, $rooms);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid())
        {
            $floor=$form->get('Floor');
            $floor=$floor->getData();

            $assigned=$form->get('isAssigned');
            $assigned=$assigned->getData();

            $searchR=$form->get('SearchRoom');
            $searchR=$searchR->getData();
            $searchR=strtoupper($searchR);

            $searchAS=$form->get('SearchAS');
            $searchAS=$searchAS->getData();
            $searchAS=strtoupper($searchAS);
            return $this->render('dashboard/admin.html.twig', [
                'rooms' => $rooms,
                'acquisitionSystems' => $acquisitionSystems,
                'floor'=>$floor,
                'assigned'=>$assigned,
                'searchR'=>$searchR,
                'searchAS'=>$searchAS,
                'form'=>$form,
            ]);
        }

        return $this->render('dashboard/admin.html.twig', [
            'rooms' => $rooms,
            'acquisitionSystems' => $acquisitionSystems,
            'floor'=>null,
            'assigned'=>null,
            'searchR'=>null,
            'searchAS'=>null,
            'form'=>$form,
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