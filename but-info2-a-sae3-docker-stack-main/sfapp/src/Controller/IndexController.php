<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class IndexController extends AbstractController
{
    /**
     * Affiche la page d'accueil de connexion.
     *
     * @Route('/login', name='app_index')
     * @return Response
     */
    #[Route('/login', name: 'app_index')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser() && in_array("ROLE_ADMIN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_admin_dashboard')->send();
        }

        else if ($this->getUser() && in_array("ROLE_TECHNICIAN", $this->getUser()->getRoles())) {
            return $this->redirectToRoute('app_tech_dashboard')->send();
        }

        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('index/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route('/logout', name: 'app_logout')]
    public function logout(): Response
    {
        return $this->redirectToRoute('app_index');
    }
}
