<?php

namespace App\Controller;

use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TechController extends AbstractController
{
    /**
     * @Route('/tech-dashboard/acquisition-system/{id}/install', name: 'app_tech_install_acquisition_system')
     *
     * Installe un système d'acquisition en mettant à jour l'attribut isInstalled à 1.
     *
     * @param int $id L'identifiant du système d'acquisition à installer
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @return RedirectResponse
     */
    #[Route('/tech-dashboard/acquisition-system/{id}/install', name: 'app_tech_install_acquisition_system')]
    #[IsGranted("ROLE_TECHNICIAN")]
    public function installAcquisitionSystem(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();

        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        // Récupérer le système d'acquisition par son ID
        $acquisitionSystem = $acquisitionSystemRepository->find($id);

        if (!$acquisitionSystem) {
            // Gérer le cas où le système d'acquisition n'est pas trouvé
            throw $this->createNotFoundException('Le système d\'acquisition n\'existe pas');
        }

        // Mettre à jour l'attribut isInstalled à 1
        $acquisitionSystem->setIsInstalled(true);

        // Mettre à jour l'état du système d'acquisition à 0
        $acquisitionSystem->setState(0);

        // Enregistrer les modifications
        $entityManager->persist($acquisitionSystem);
        $entityManager->flush();

        // Ajouter un message flash
        $this->addFlash('success', 'Le système d\'acquisition a été installé avec succès.');

        // Rediriger vers une autre page après l'installation
        return $this->redirectToRoute('app_tech_dashboard');
    }

    /**
     * @Route('/tech-dashboard/acquisition-system/{id}/install', name: 'app_tech_uninstall_acquisition_system')
     *
     * Désinstalle un système d'acquisition en mettant à jour l'attribut isInstalled à 0.
     *
     * @param int $id L'identifiant du système d'acquisition à installer
     * @param ManagerRegistry $doctrine Le registre de gestionnaire d'entités
     * @return RedirectResponse
     */
    #[Route('/tech-dashboard/acquisition-system/{id}/uninstall', name: 'app_tech_uninstall_acquisition_system')]
    #[IsGranted("ROLE_TECHNICIAN")]
    public function uninstallAcquisitionSystem(int $id, ManagerRegistry $doctrine): RedirectResponse
    {
        $entityManager = $doctrine->getManager();

        $acquisitionSystemRepository = $entityManager->getRepository('App\Entity\AcquisitionSystem');

        // Récupérer le système d'acquisition par son ID
        $acquisitionSystem = $acquisitionSystemRepository->find($id);

        if (!$acquisitionSystem) {
            // Gérer le cas où le système d'acquisition n'est pas trouvé
            throw $this->createNotFoundException('Le système d\'acquisition n\'existe pas');
        }

        // Mettre à jour l'attribut isInstalled à 0
        $acquisitionSystem->setIsInstalled(false);

        // Mettre à jour l'état du système d'acquisition à 0
        $acquisitionSystem->setState(0);

        // Enregistrer les modifications
        $entityManager->persist($acquisitionSystem);
        $entityManager->flush();

        // Ajouter un message flash
        $this->addFlash('success', 'Le système d\'acquisition a été désinstallé avec succès.');

        // Rediriger vers une autre page après l'installation
        return $this->redirectToRoute('app_tech_dashboard');
    }
}
