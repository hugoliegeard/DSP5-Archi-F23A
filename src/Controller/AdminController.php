<?php

namespace App\Controller;

use App\Entity\Event;
use App\Entity\User;
use App\Form\EventType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/administration')]
class AdminController extends AbstractController
{
    #[Route('/gestion-des-utilisateurs.html', name: 'admin_users', methods: 'GET')]
    public function users(UserRepository $userRepository): Response
    {
        # Récupération des utilisateurs de la BDD
        $users = $userRepository->findAll();

        # Transmission à la vue
        return $this->render('admin/users.html.twig', [
           'users' => $users
        ]);
    }
}