<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AcquisitionSystemController extends AbstractController
{
    #[Route('/assign-acquisition-system', name: 'app_acquisition_system')]
    public function assign(): Response
    {
        return $this->render('acquisition_system/index.html.twig', [
            'controller_name' => 'AcquisitionSystemController',
        ]);
    }

    #[Route('/manage-acquisition-system', name: 'app_acquisition_system')]
    public function manage(): Response
    {
        return $this->render('acquisition_system/index.html.twig', [
            'controller_name' => 'AcquisitionSystemController',
        ]);
    }
}
 